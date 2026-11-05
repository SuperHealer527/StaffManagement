<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);

        $reportEvents = Event::query()
            ->with(['client', 'reportQuestions', 'reportSubmissions.user', 'assignments.user'])
            ->where('report_required', true)
            ->orderByDesc('event_date')
            ->get()
            ->when($request->input('status') === 'pending', fn ($events) => $events->filter(fn (Event $event): bool => $event->reportSubmissions->whereNotNull('submitted_at')->isEmpty()))
            ->when($request->input('status') === 'submitted', fn ($events) => $events->filter(fn (Event $event): bool => $event->reportSubmissions->whereNotNull('submitted_at')->isNotEmpty()))
            ->when($request->input('completion') === 'attention', fn ($events) => $events->filter(fn (Event $event): bool => $event->reportSubmissions->contains(fn ($submission): bool => in_array(data_get($submission->answers, 'completion'), ['issue', 'incident'], true))))
            ->values();

        $selectedEvent = $reportEvents->firstWhere('id', (int) $request->integer('event_id')) ?? $reportEvents->first();

        return view('static-pages.ReportView', compact('reportEvents', 'selectedEvent'));
    }
}
