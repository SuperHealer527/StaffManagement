<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\NotificationLog;
use App\Models\PhoneLog;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = request()->user();
        if (in_array($user->role, ['staff', 'temporary', 'trainer'], true)) {
            return app(StaffPortalController::class)->home(request());
        }

        $template = match ($user->role) {
            'semi_employee' => 'SubMain',
            default => 'Main',
        };

        $events = Event::query()
            ->with('client')
            ->withCount(['assignments as confirmed_count' => fn ($query) => $query->whereIn('status', ['confirmed', 'completed'])])
            ->whereBetween('event_date', [today(), today()->addDays(28)])
            ->orderBy('event_date')
            ->get();
        $shortageEvents = $events
            ->where('event_date', '<=', today()->addDays(14))
            ->filter(fn (Event $event): bool => $event->confirmed_count < $event->required_staff);
        $todayAssignments = Assignment::query()
            ->with(['event', 'user'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('same_day_confirmation', '!=', 'confirmed')
            ->whereHas('event', fn ($query) => $query->whereDate('event_date', today()))
            ->get()
            ->filter(function (Assignment $assignment): bool {
                $meetingAt = Carbon::parse($assignment->event->event_date->toDateString().' '.$assignment->event->meeting_time, 'Asia/Tokyo');

                return now('Asia/Tokyo')->greaterThanOrEqualTo($meetingAt->subHours(2));
            });
        $sevenDayAssignments = Assignment::query()
            ->with(['event', 'user'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('seven_day_confirmation', ['not_sent', 'sent'])
            ->whereHas('event', fn ($query) => $query->whereBetween('event_date', [today(), today()->addDays(7)]))
            ->orderBy(Event::select('event_date')->whereColumn('events.id', 'assignments.event_id'))
            ->get();
        $pendingAssignments = Assignment::query()
            ->with(['event', 'user'])
            ->whereIn('status', ['requested', 'unknown'])
            ->oldest('requested_at')
            ->get();
        $myAssignments = Assignment::query()
            ->with('event')
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereHas('event', fn ($query) => $query->whereDate('event_date', '>=', today()))
            ->orderBy(Event::select('event_date')->whereColumn('events.id', 'assignments.event_id'))
            ->limit(5)
            ->get();
        $waitingPayments = Assignment::query()->where('payment_status', 'waiting');

        return view("static-pages.{$template}", [
            'dashboard' => [
                'events14Count' => $events->where('event_date', '<=', today()->addDays(14))->count(),
                'events28Count' => $events->count(),
                'shortageEvents' => $shortageEvents,
                'waitingPaymentCount' => (clone $waitingPayments)->count(),
                'waitingPaymentTotal' => (int) (clone $waitingPayments)->selectRaw('coalesce(sum(fee + transportation + reimbursement - withholding_tax), 0) as total')->value('total'),
                'failedNotificationCount' => NotificationLog::query()->where('status', 'failed')->count(),
                'phoneMessageCount' => PhoneLog::query()->where('recipient_id', $user->id)->where('status', 'callback_required')->count(),
                'staleRequestCount' => $pendingAssignments->filter(fn (Assignment $assignment): bool => $assignment->requested_at?->lessThanOrEqualTo(now()->subDays(3)) ?? false)->count(),
                'todayAssignments' => $todayAssignments,
                'sevenDayAssignments' => $sevenDayAssignments,
                'pendingAssignments' => $pendingAssignments,
                'myPendingCount' => Assignment::query()->where('user_id', $user->id)->whereIn('status', ['requested', 'unknown'])->count(),
                'myYearWorkCount' => Assignment::query()->where('user_id', $user->id)->whereIn('status', ['confirmed', 'completed'])->whereHas('event', fn ($query) => $query->whereYear('event_date', today()->year)->whereDate('event_date', '<=', today()))->count(),
                'myAssignments' => $myAssignments,
                'expiringQualifications' => Qualification::query()->with('user')->whereBetween('expires_at', [today(), today()->addDays(60)])->orderBy('expires_at')->get(),
            ],
        ]);
    }
}
