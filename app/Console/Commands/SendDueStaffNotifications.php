<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Interview;
use App\Models\NotificationLog;
use App\Models\ReportSubmission;
use App\Services\NotificationDeliveryService;
use App\Services\SecureActionLinkService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('staff-management:send-due-notifications')]
#[Description('出勤確認・報告書・面談の期限通知を送信します')]
class SendDueStaffNotifications extends Command
{
    public function handle(
        SecureActionLinkService $links,
        NotificationDeliveryService $delivery,
    ): int {
        $sentCount = 0;
        $now = now('Asia/Tokyo');

        Assignment::query()
            ->with(['event', 'user'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereHas('event', fn ($query) => $query->whereDate('event_date', '>=', $now->copy()->subDay()->toDateString()))
            ->chunkById(100, function ($assignments) use ($delivery, $links, $now, &$sentCount): void {
                foreach ($assignments as $assignment) {
                    $sentCount += $this->sendAttendanceNotifications($assignment, $now, $links, $delivery);
                    $sentCount += $this->sendReportNotification($assignment, $now, $links, $delivery);
                }
            });

        Interview::query()
            ->with('subject')
            ->whereNotNull('scheduled_at')
            ->whereDate('scheduled_at', $now->copy()->addDay()->toDateString())
            ->chunkById(100, function ($interviews) use ($delivery, $now, &$sentCount): void {
                if ($now->hour < 9) {
                    return;
                }

                foreach ($interviews as $interview) {
                    if ($this->alreadyLogged($interview, 'interview_reminder')) {
                        continue;
                    }

                    $delivery->deliver(
                        $interview->subject,
                        $interview,
                        'interview_reminder',
                        '面談の前日のお知らせ',
                        sprintf('明日 %s から面談があります。方法：%s%s', $interview->scheduled_at->format('H:i'), $interview->method, $interview->location ? '／'.$interview->location : ''),
                    );
                    $sentCount++;
                }
            });

        $this->info($sentCount.'件の期限通知を処理しました。');

        return self::SUCCESS;
    }

    private function sendAttendanceNotifications(
        Assignment $assignment,
        Carbon $now,
        SecureActionLinkService $links,
        NotificationDeliveryService $delivery,
    ): int {
        $event = $assignment->event;
        $eventDate = Carbon::parse($event->event_date->toDateString(), 'Asia/Tokyo')->startOfDay();
        $daysUntilEvent = (int) round($now->copy()->startOfDay()->diffInDays($eventDate, false));
        $sentCount = 0;

        if (in_array($daysUntilEvent, [7, 5, 3], true)
            && $now->hour >= 9
            && in_array($assignment->seven_day_confirmation, ['not_sent', 'sent'], true)) {
            $type = 'attendance_'.$daysUntilEvent.'_day';
            if (! $this->alreadyLogged($assignment, $type)) {
                $token = $links->createUntil($assignment->user, 'attendance_seven_day', $assignment, $event->event_date->copy()->endOfDay());
                $delivery->deliver(
                    $assignment->user,
                    $assignment,
                    $type,
                    $daysUntilEvent === 7 ? '1週間前の出勤確認' : $daysUntilEvent.'日前の出勤確認（未回答）',
                    sprintf('%s（%s）の出勤確認をお願いします。集合 %s', $event->name ?: $event->business_name, $event->event_date->isoFormat('M/D(ddd)'), substr($event->meeting_time, 0, 5)),
                    route('public-actions.show', $token),
                    '出勤を確認する',
                );
                if ($assignment->seven_day_confirmation === 'not_sent') {
                    $assignment->update(['seven_day_confirmation' => 'sent']);
                }
                $sentCount++;
            }
        }

        $meetingAt = Carbon::parse($event->event_date->toDateString().' '.$event->meeting_time, 'Asia/Tokyo');
        if ($daysUntilEvent === 0
            && $now->greaterThanOrEqualTo($meetingAt->copy()->subHours(4))
            && $assignment->same_day_confirmation === 'not_sent'
            && ! $this->alreadyLogged($assignment, 'attendance_same_day')) {
            $token = $links->createUntil($assignment->user, 'attendance_same_day', $assignment, $event->event_date->copy()->endOfDay());
            $delivery->deliver(
                $assignment->user,
                $assignment,
                'attendance_same_day',
                '当日の出勤確認',
                sprintf('本日の%sの出勤確認をお願いします。集合 %s／%s', $event->name ?: $event->business_name, substr($event->meeting_time, 0, 5), $event->meeting_place ?: '集合場所未設定'),
                route('public-actions.show', $token),
                '出勤を確認する',
            );
            $assignment->update(['same_day_confirmation' => 'sent']);
            $sentCount++;
        }

        return $sentCount;
    }

    private function sendReportNotification(
        Assignment $assignment,
        Carbon $now,
        SecureActionLinkService $links,
        NotificationDeliveryService $delivery,
    ): int {
        $event = $assignment->event;
        if (! $assignment->is_report_representative || ! $event->report_required) {
            return 0;
        }

        $dismissalAt = Carbon::parse($event->event_date->toDateString().' '.$event->dismissal_time, 'Asia/Tokyo');
        $submitted = ReportSubmission::query()
            ->where('event_id', $event->id)
            ->where('user_id', $assignment->user_id)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($now->lessThan($dismissalAt) || $submitted || $this->alreadyLogged($assignment, 'report_request')) {
            return 0;
        }

        $token = $links->createUntil($assignment->user, 'report_submission', $assignment, $event->event_date->copy()->addDays(7)->endOfDay());
        $delivery->deliver(
            $assignment->user,
            $assignment,
            'report_request',
            '報告書のご提出',
            ($event->name ?: $event->business_name).'の報告書を提出してください。',
            route('public-actions.show', $token),
            '報告書を提出する',
        );

        return 1;
    }

    private function alreadyLogged(Assignment|Interview $context, string $type): bool
    {
        return NotificationLog::query()
            ->where('notifiable_context_type', $context->getMorphClass())
            ->where('notifiable_context_id', $context->getKey())
            ->where('type', $type)
            ->exists();
    }
}
