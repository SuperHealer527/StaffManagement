<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Qualification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $events = Event::query()->with('client')->withCount(['assignments as confirmed_count' => fn ($query) => $query->where('status', 'confirmed')])->orderBy('event_date')->limit(6)->get();

        return view('dashboard', [
            'events' => $events,
            'shortageCount' => $events->filter(fn (Event $event) => $event->confirmed_count < $event->required_staff)->count(),
            'pendingCount' => Assignment::query()->where('status', 'requested')->count(),
            'qualificationCount' => Qualification::query()->whereBetween('expires_at', [today(), today()->addDays(60)])->count(),
        ]);
    }
}
