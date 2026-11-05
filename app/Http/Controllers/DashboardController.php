<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = request()->user();
        $events = Event::query()
            ->with(['client', 'assignments.user'])
            ->withCount(['assignments as confirmed_count' => fn ($query) => $query->where('status', 'confirmed')])
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->limit(12)
            ->get();

        /** @var Collection<int, Assignment> $myAssignments */
        $myAssignments = Assignment::query()
            ->with('event.client')
            ->where('user_id', $user->id)
            ->whereHas('event', fn ($query) => $query->whereDate('event_date', '>=', today()))
            ->orderBy(Event::select('event_date')->whereColumn('events.id', 'assignments.event_id'))
            ->get();

        $uncheckedAssignments = Assignment::query()
            ->with(['event', 'user'])
            ->where('status', 'confirmed')
            ->where('same_day_confirmation', '!=', 'confirmed')
            ->whereHas('event', fn ($query) => $query->whereDate('event_date', today()))
            ->limit(2)
            ->get();

        $sevenDayUncheckedAssignments = Assignment::query()
            ->with(['event', 'user'])
            ->where('status', 'confirmed')
            ->where('seven_day_confirmation', '!=', 'confirmed')
            ->whereHas('event', fn ($query) => $query->whereBetween('event_date', [today(), today()->addDays(7)]))
            ->limit(2)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'events' => $events,
            'twoWeekCount' => $events->where('event_date', '<=', today()->addDays(14))->count(),
            'fourWeekCount' => $events->where('event_date', '<=', today()->addDays(28))->count(),
            'shortages' => $events->filter(fn (Event $event) => $event->confirmed_count < $event->required_staff),
            'pendingCount' => Assignment::query()->where('status', 'requested')->count(),
            'staleAssignments' => Assignment::query()->with(['event', 'user'])->where('status', 'requested')->where('requested_at', '<=', now()->subDays(3))->limit(3)->get(),
            'expiringQualifications' => Qualification::query()->with('user')->whereBetween('expires_at', [today(), today()->addDays(60)])->limit(4)->get(),
            'myAssignments' => $myAssignments,
            'uncheckedAssignments' => $uncheckedAssignments,
            'sevenDayUncheckedAssignments' => $sevenDayUncheckedAssignments,
            'pendingPaymentCount' => Assignment::query()->where('payment_status', 'waiting')->count(),
            'pendingPaymentTotal' => Assignment::query()->where('payment_status', 'waiting')->get()->sum('total_payment'),
        ]);
    }
}
