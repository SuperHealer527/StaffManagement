<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\NotificationLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentWorkflowService
{
    public function __construct(
        private NotificationDeliveryService $notificationDeliveryService,
        private SecureActionLinkService $secureActionLinkService,
    ) {}

    /** @var list<string> */
    private const OPEN_RESPONSE_STATUSES = ['requested', 'unknown', 'accepted'];

    /** @var list<string> */
    private const PAYMENT_ELIGIBLE_STATUSES = ['confirmed', 'completed'];

    public function changeStatus(Assignment $assignment, string $newStatus): Assignment
    {
        if ($assignment->status === $newStatus) {
            return $assignment->fresh(['event', 'user']);
        }

        $updatedAssignment = DB::transaction(function () use ($assignment, $newStatus): Assignment {
            $lockedAssignment = Assignment::query()->lockForUpdate()->findOrFail($assignment->id);
            $event = Event::query()->lockForUpdate()->findOrFail($lockedAssignment->event_id);

            $this->ensureStatusChangeIsAllowed($event, $lockedAssignment, $newStatus);

            $updates = ['status' => $newStatus, 'responded_at' => now()];
            if ($newStatus === 'confirmed' && $lockedAssignment->payment_status !== 'paid') {
                $updates['payment_status'] = 'unconfirmed';
                $daysUntilEvent = (int) round(today()->diffInDays($event->event_date, false));
                if ($daysUntilEvent < 7 || ($daysUntilEvent === 7 && now('Asia/Tokyo')->hour >= 9)) {
                    $updates['seven_day_confirmation'] = 'not_applicable';
                }
            }
            if ($newStatus === 'completed' && $lockedAssignment->payment_status !== 'paid') {
                $updates['payment_status'] = 'waiting';
            }
            if (! in_array($newStatus, self::PAYMENT_ELIGIBLE_STATUSES, true)) {
                $updates['is_day_leader'] = false;
                $updates['is_report_representative'] = false;
            }

            $lockedAssignment->update($updates);
            $this->synchronizeEventStatus($event, $lockedAssignment);

            return $lockedAssignment->fresh(['event', 'user']);
        });

        if (in_array($newStatus, ['confirmed', 'rejected', 'cancelled'], true)) {
            $this->sendStatusNotification($updatedAssignment, $newStatus);
        }

        return $updatedAssignment;
    }

    /** @return array{events: int, assignments: int} */
    public function synchronizePastEvents(): array
    {
        $eventCount = 0;
        $assignmentCount = 0;

        Event::query()
            ->whereDate('event_date', '<', today())
            ->whereNotIn('status', ['draft', 'cancelled', 'completed'])
            ->with('assignments')
            ->chunkById(100, function ($events) use (&$eventCount, &$assignmentCount): void {
                foreach ($events as $event) {
                    DB::transaction(function () use ($event, &$eventCount, &$assignmentCount): void {
                        foreach ($event->assignments as $assignment) {
                            if (in_array($assignment->status, self::OPEN_RESPONSE_STATUSES, true)) {
                                $assignment->update(['status' => 'rejected', 'responded_at' => now()]);
                                $assignmentCount++;
                            } elseif ($assignment->status === 'confirmed') {
                                $updates = ['status' => 'completed'];
                                if ($assignment->payment_status !== 'paid') {
                                    $updates['payment_status'] = 'waiting';
                                }
                                $assignment->update($updates);
                                $assignmentCount++;
                            }
                        }

                        $event->update(['status' => 'completed']);
                        $eventCount++;
                    });
                }
            });

        return ['events' => $eventCount, 'assignments' => $assignmentCount];
    }

    public function synchronizeEventStatus(Event $event, ?Assignment $changedAssignment = null): void
    {
        if (in_array($event->status, ['cancelled', 'completed'], true)) {
            return;
        }

        $confirmedCount = $event->assignments()->whereIn('status', Assignment::STAFFING_STATUSES)->count();

        if ($confirmedCount >= $event->required_staff) {
            $event->update(['status' => 'confirmed']);
            $overflowAssignments = $event->assignments()
                ->when($changedAssignment !== null, fn ($query) => $query->where('id', '!=', $changedAssignment->id))
                ->whereIn('status', Assignment::AWAITING_RESPONSE_STATUSES)
                ->get();

            foreach ($overflowAssignments as $overflowAssignment) {
                $overflowAssignment->update(['status' => 'rejected', 'responded_at' => now()]);
                $this->sendStatusNotification($overflowAssignment->load(['event', 'user']), 'rejected');
            }

            return;
        }

        if ($event->status === 'confirmed' && $event->event_date->greaterThanOrEqualTo(today())) {
            $event->update(['status' => 'recruiting']);
        }
    }

    private function ensureStatusChangeIsAllowed(Event $event, Assignment $assignment, string $newStatus): void
    {
        if ($event->status === 'cancelled') {
            throw ValidationException::withMessages(['status' => '中止済みイベントのアサイン状態は変更できません。']);
        }

        if ($newStatus === 'confirmed' && in_array($assignment->status, ['declined', 'rejected', 'cancelled', 'absent', 'completed'], true)) {
            throw ValidationException::withMessages(['status' => '終了済みのアサインを確定には戻せません。']);
        }

        if ($newStatus === 'confirmed' && ! in_array($assignment->status, self::OPEN_RESPONSE_STATUSES, true)) {
            throw ValidationException::withMessages(['status' => '依頼中・未定・承諾のアサインだけを確定できます。']);
        }

        if ($newStatus === 'rejected' && ! in_array($assignment->status, self::OPEN_RESPONSE_STATUSES, true)) {
            throw ValidationException::withMessages(['status' => '回答待ちのアサインだけを見送りにできます。']);
        }

        if ($newStatus === 'cancelled' && ! in_array($assignment->status, [...self::OPEN_RESPONSE_STATUSES, 'confirmed'], true)) {
            throw ValidationException::withMessages(['status' => '依頼中または確定済みのアサインだけを取り消せます。']);
        }

        if ($newStatus === 'absent'
            && (! in_array($assignment->status, self::PAYMENT_ELIGIBLE_STATUSES, true) || $event->event_date->isFuture())) {
            throw ValidationException::withMessages(['status' => 'イベント当日以降の確定済み担当者だけを欠勤にできます。']);
        }

        if ($newStatus === 'completed' && $assignment->status !== 'confirmed') {
            throw ValidationException::withMessages(['status' => '確定済みのアサインだけを勤務完了にできます。']);
        }

        if (in_array($newStatus, self::PAYMENT_ELIGIBLE_STATUSES, true) && $event->status === 'draft') {
            throw ValidationException::withMessages(['status' => '下書きイベントのアサインは確定できません。']);
        }
    }

    private function sendStatusNotification(Assignment $assignment, string $status): void
    {
        $event = $assignment->event;
        $eventName = $event->name ?: $event->business_name;

        if ($status === 'confirmed') {
            $expiresAt = $event->event_date->copy()->endOfDay();
            $actionUrl = null;
            if ($expiresAt->isFuture()) {
                $detailsToken = $this->secureActionLinkService->createUntil(
                    $assignment->user,
                    'assignment_response',
                    $assignment,
                    $expiresAt,
                );
                $actionUrl = route('public-actions.show', $detailsToken);
            }
            $this->notificationDeliveryService->deliver(
                $assignment->user,
                $assignment,
                'assignment_confirmed',
                '確定のお知らせ：'.$eventName,
                sprintf('%s（%s）への参加が確定しました。集合%s／%s', $eventName, $event->event_date->format('Y年n月j日'), substr($event->meeting_time, 0, 5), $event->meeting_place ?: '集合場所未設定'),
                $actionUrl,
                '詳しく見る',
            );
            $this->sendTemporaryProfileRequestIfNeeded($assignment);

            return;
        }

        $rejected = $status === 'rejected';
        $this->notificationDeliveryService->deliver(
            $assignment->user,
            $assignment,
            $rejected ? 'assignment_rejected_capacity' : 'assignment_cancelled',
            $rejected ? '見送りのお知らせ：'.$eventName : '取消のお知らせ：'.$eventName,
            $rejected
                ? $eventName.'は必要な人数がそろったため、今回は見送りとなりました。'
                : $eventName.'のアサインは取消となりました。',
        );
    }

    private function sendTemporaryProfileRequestIfNeeded(Assignment $assignment): void
    {
        $user = $assignment->user;
        if ($user->role !== 'temporary' || (filled($user->address) && filled($user->bank_details))) {
            return;
        }

        $alreadySent = NotificationLog::query()
            ->where('user_id', $user->id)
            ->where('type', 'temporary_profile')
            ->exists();
        if ($alreadySent) {
            return;
        }

        $token = $this->secureActionLinkService->createUntil(
            $user,
            'temporary_profile',
            $user,
            Carbon::now()->addDays(7),
        );
        $this->notificationDeliveryService->deliver(
            $user,
            $user,
            'temporary_profile',
            '振込先と住所の入力のお願い',
            'お支払いのため、振込先口座と住所を入力してください。リンクは1回だけ使用できます。',
            route('public-actions.show', $token),
            '入力する',
        );
    }
}
