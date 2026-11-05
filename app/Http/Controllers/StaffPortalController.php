<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmOwnAttendanceRequest;
use App\Http\Requests\RespondOwnAssignmentRequest;
use App\Http\Requests\StoreOwnReportRequest;
use App\Models\Assignment;
use App\Models\ReportSubmission;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPortalController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function home(Request $request): View
    {
        $user = $request->user();
        $assignments = $user->assignments()->with(['event.client', 'event.reportQuestions', 'event.assignments.user'])->get();
        $openRequests = $assignments->whereIn('status', ['requested', 'unknown', 'accepted'])->sortBy(fn ($item) => $item->event->event_date);
        $upcomingAssignments = $assignments->where('status', 'confirmed')->filter(fn ($item) => $item->event->event_date->greaterThanOrEqualTo(today()))->sortBy(fn ($item) => $item->event->event_date);
        $attendanceAssignment = $upcomingAssignments->first(fn ($item) => $item->event->event_date->lessThanOrEqualTo(today()->addDays(7)) && in_array($item->seven_day_confirmation, ['not_sent', 'sent'], true));
        $reportAssignment = $assignments->first(fn ($item) => $item->is_report_representative && $item->event->report_required && $item->event->event_date->lessThanOrEqualTo(today()) && ! ReportSubmission::query()->where('event_id', $item->event_id)->where('user_id', $user->id)->whereNotNull('submitted_at')->exists());
        $yearWorkCount = $assignments->whereIn('status', ['confirmed', 'completed'])->filter(fn ($item) => $item->event->event_date->year === today()->year)->count();

        return view('static-pages.Staff', compact('user', 'openRequests', 'upcomingAssignments', 'attendanceAssignment', 'reportAssignment', 'yearWorkCount'));
    }

    public function requests(Request $request): View
    {
        $tab = $request->input('tab', 'pending');
        $query = $request->user()->assignments()->with('event.client');
        match ($tab) {
            'upcoming' => $query->where('status', 'confirmed')->whereHas('event', fn ($eventQuery) => $eventQuery->whereDate('event_date', '>=', today())),
            'past' => $query->where(fn ($assignmentQuery) => $assignmentQuery->whereIn('status', ['declined', 'rejected', 'cancelled', 'absent', 'completed'])->orWhereHas('event', fn ($eventQuery) => $eventQuery->whereDate('event_date', '<', today()))),
            default => $query->whereIn('status', ['requested', 'unknown', 'accepted']),
        };
        $myAssignments = $query->get()->sortBy(fn ($item) => $item->event->event_date)->values();

        return view('static-pages.MyRequests', compact('myAssignments', 'tab'));
    }

    public function respond(RespondOwnAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $oldStatus = $assignment->status;
        $assignment->update(['status' => $request->validated('response'), 'responded_at' => now()]);
        $this->auditService->record($assignment, 'respond_to_assignment', ['status' => $oldStatus], ['status' => $assignment->status]);

        return back()->with('status', '回答を保存しました。確定前であれば変更できます。');
    }

    public function confirmAttendance(ConfirmOwnAttendanceRequest $request, Assignment $assignment, string $period): RedirectResponse
    {
        abort_unless(in_array($period, ['seven-day', 'same-day'], true), 404);
        $field = $period === 'seven-day' ? 'seven_day_confirmation' : 'same_day_confirmation';
        $assignment->update([$field => 'confirmed']);
        $this->auditService->record($assignment, 'confirm_attendance', null, [$field => 'confirmed']);

        return back()->with('status', '出勤を確認しました。');
    }

    public function payments(Request $request): View
    {
        $year = max(2000, min(2100, (int) $request->input('year', today()->year)));
        $paymentAssignments = $request->user()->assignments()
            ->with('event.client')
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereHas('event', fn ($query) => $query->whereYear('event_date', $year))
            ->get()
            ->sortByDesc(fn ($item) => $item->event->event_date)
            ->values();

        return view('static-pages.Payslip', compact('paymentAssignments', 'year'));
    }

    public function report(Request $request, Assignment $assignment): View
    {
        $assignment->load(['event.client', 'event.reportQuestions']);
        abort_unless($request->user()?->id === $assignment->user_id && $assignment->is_report_representative && $assignment->event->report_required, 403);
        $reportSubmission = ReportSubmission::query()->where('event_id', $assignment->event_id)->where('user_id', $assignment->user_id)->first();

        return view('static-pages.Report', ['assignment' => $assignment, 'event' => $assignment->event, 'user' => $request->user(), 'reportSubmission' => $reportSubmission, 'authenticatedReport' => true]);
    }

    public function storeReport(StoreOwnReportRequest $request, Assignment $assignment): RedirectResponse
    {
        $values = $request->validated();
        $submission = ReportSubmission::query()->updateOrCreate(
            ['event_id' => $assignment->event_id, 'user_id' => $assignment->user_id],
            ['answers' => ['completion' => $values['completion']] + ($values['answers'] ?? []), 'submitted_at' => now()],
        );
        $this->auditService->record($submission, 'submit_report');

        return redirect()->route('my.home')->with('status', '報告書を提出しました。');
    }
}
