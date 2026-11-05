<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Requests\UpdateEventAssignmentLeadersRequest;
use App\Models\Assignment;
use App\Models\Event;
use App\Models\Qualification;
use App\Models\User;
use App\Services\AssignmentWorkflowService;
use App\Services\NotificationDeliveryService;
use App\Services\SecureActionLinkService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct(
        private AssignmentWorkflowService $assignmentWorkflowService,
        private SecureActionLinkService $secureActionLinkService,
        private NotificationDeliveryService $notificationDeliveryService,
    ) {}

    public function overview(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canManageAssignments(), 403);

        $event = Event::query()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->first();

        return $event === null
            ? redirect()->route('events.index')->with('status', 'アサインできる今後のイベントはありません。')
            : redirect()->route('events.assignments.index', $event);
    }

    public function index(Request $request, Event $event): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);

        $event->load(['client', 'assignments.user.qualifications']);
        $assignedUserIds = $event->assignments->pluck('user_id');
        $weekday = $event->event_date->dayOfWeek;

        $candidateQuery = User::query()
            ->where(fn ($query) => $query->where('account_status', 'active')->orWhere(fn ($temporaryQuery) => $temporaryQuery->where('role', 'temporary')->where('account_status', 'no_login')))
            ->whereIn('role', ['employee', 'semi_employee', 'staff', 'trainer', 'temporary'])
            ->whereNotIn('id', $assignedUserIds)
            ->with(['qualifications', 'availabilities', 'assignments' => fn ($query) => $query
                ->where('status', 'confirmed')
                ->whereHas('event', fn (Builder $eventQuery) => $eventQuery->whereDate('event_date', $event->event_date))
                ->with('event')])
            ->withCount([
                'assignments as client_work_count' => fn ($query) => $query
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('client_id', $event->client_id)),
                'assignments as year_work_count' => fn ($query) => $query
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->whereHas('event', fn (Builder $eventQuery) => $eventQuery->whereYear('event_date', $event->event_date->year)),
            ])
            ->when($request->filled('keyword'), fn ($query) => $query->where('name', 'like', '%'.$request->string('keyword')->trim().'%'))
            ->when($request->filled('qualification'), fn ($query) => $query->whereHas('qualifications', fn ($qualificationQuery) => $qualificationQuery->where('name', $request->string('qualification'))))
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->string('gender')))
            ->when($request->boolean('student'), fn ($query) => $query->where('is_student', true))
            ->when($request->boolean('has_car'), fn ($query) => $query->where('has_car', true))
            ->when($request->boolean('available'), fn ($query) => $query->whereHas('availabilities', fn ($availabilityQuery) => $availabilityQuery
                ->where('weekday', $weekday)
                ->where('availability', 'available')));

        match ($request->input('sort', 'client_count')) {
            'year_count' => $candidateQuery->orderBy('year_work_count')->orderBy('name'),
            'name' => $candidateQuery->orderBy('name'),
            default => $candidateQuery->orderByDesc('client_work_count')->orderBy('name'),
        };

        $candidates = $candidateQuery->get();
        if ($request->boolean('exclude_overlap')) {
            $candidates = $candidates->filter(fn (User $candidate): bool => $this->overlappingAssignment($candidate, $event) === null)->values();
        }

        $qualifications = Qualification::query()->distinct()->orderBy('name')->pluck('name');

        return view('static-pages.Assign', compact('event', 'candidates', 'qualifications'));
    }

    public function store(StoreAssignmentRequest $request, Event $event): RedirectResponse
    {
        $values = $request->validated();
        $confirmedDirectly = $request->boolean('confirmed_directly');
        $this->ensureEventAcceptsAssignment($event, $confirmedDirectly);

        $candidate = User::query()
            ->whereKey($values['user_id'])
            ->where(fn ($query) => $query->where('account_status', 'active')->orWhere(fn ($temporaryQuery) => $temporaryQuery->where('role', 'temporary')->where('account_status', 'no_login')))
            ->whereIn('role', ['employee', 'semi_employee', 'staff', 'trainer', 'temporary'])
            ->firstOrFail();

        if ($event->assignments()->where('user_id', $candidate->id)->exists()) {
            throw ValidationException::withMessages(['user_id' => 'このスタッフはすでにアサインされています。']);
        }

        $assignment = DB::transaction(function () use ($event, $candidate, $values, $confirmedDirectly): Assignment {
            $assignment = $event->assignments()->create([
                'user_id' => $candidate->id,
                'status' => $confirmedDirectly ? 'confirmed' : 'requested',
                'special_notes' => $values['special_notes'] ?? null,
                'fee' => $values['fee'],
                'transportation' => $values['transportation'],
                'payment_status' => $confirmedDirectly ? 'unconfirmed' : 'waiting',
                'requested_at' => $confirmedDirectly ? null : now(),
                'responded_at' => $confirmedDirectly ? now() : null,
            ]);

            return $assignment->load(['event.client', 'user']);
        });

        if ($confirmedDirectly) {
            $assignment = $this->assignmentWorkflowService->changeStatus($assignment, 'confirmed');
        } else {
            $this->sendAssignmentRequest($assignment);
        }

        return back()->with('status', $confirmedDirectly ? '確定メンバーとして追加しました。' : '勤務依頼を送信しました。');
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $assignment->loadMissing('event');
        $data = $request->validated();
        if (! $request->user()->isEmployee()) {
            unset($data['fee'], $data['transportation']);
        }
        $assignment->update(Arr::except($data, 'status'));
        $this->assignmentWorkflowService->changeStatus($assignment, $data['status']);

        return back()->with('status', 'アサインを更新しました。');
    }

    public function updateLeaders(UpdateEventAssignmentLeadersRequest $request, Event $event): RedirectResponse
    {
        $values = $request->validated();
        $leader = $this->confirmedManagerAssignment($event, $values['day_leader_assignment_id'] ?? null, 'day_leader_assignment_id');
        $representativeId = $request->boolean('representative_same_as_leader')
            ? $leader?->id
            : ($values['report_representative_assignment_id'] ?? null);
        $representative = $this->confirmedManagerAssignment($event, $representativeId, 'report_representative_assignment_id');

        DB::transaction(function () use ($event, $leader, $representative): void {
            $event->assignments()->update(['is_day_leader' => false, 'is_report_representative' => false]);
            $leader?->update(['is_day_leader' => true]);
            $representative?->update(['is_report_representative' => true]);
        });

        return back()->with('status', '当日責任者と報告書代表者を更新しました。');
    }

    private function sendAssignmentRequest(Assignment $assignment): void
    {
        $expiresAt = $assignment->event->event_date->copy()->endOfDay();
        $plainToken = $this->secureActionLinkService->createUntil($assignment->user, 'assignment_response', $assignment, $expiresAt);
        $actionUrl = route('public-actions.show', $plainToken);
        $event = $assignment->event;
        $body = sprintf(
            "%sのお仕事をご依頼します。\n日時：%s 集合%s／解散%s\n場所：%s\n謝金：%s円／交通費：%s円%s",
            $event->name ?: $event->business_name,
            $event->event_date->format('Y年n月j日'),
            substr($event->meeting_time, 0, 5),
            substr($event->dismissal_time, 0, 5),
            $event->venue ?: $event->meeting_place ?: '未定',
            number_format($assignment->fee),
            number_format($assignment->transportation),
            $assignment->special_notes ? "\n備考：".$assignment->special_notes : '',
        );
        $this->notificationDeliveryService->deliver(
            $assignment->user,
            $assignment,
            'assignment_request',
            'お仕事のご依頼：'.($event->name ?: $event->business_name),
            $body,
            $actionUrl,
            '依頼に回答する',
        );
    }

    private function ensureEventAcceptsAssignment(Event $event, bool $confirmedDirectly): void
    {
        if ($event->status === 'cancelled' || (! $confirmedDirectly && $event->status !== 'recruiting')) {
            throw ValidationException::withMessages(['event' => $confirmedDirectly
                ? '中止済みイベントには追加できません。'
                : '募集中のイベントにだけ依頼を送信できます。']);
        }
    }

    private function confirmedManagerAssignment(Event $event, ?int $assignmentId, string $field): ?Assignment
    {
        if ($assignmentId === null) {
            return null;
        }

        $assignment = $event->assignments()->with('user')->find($assignmentId);
        if ($assignment === null || $assignment->status !== 'confirmed' || ! in_array($assignment->user->role, ['employee', 'semi_employee'], true)) {
            throw ValidationException::withMessages([$field => '確定済みの社員または準社員を選択してください。']);
        }

        return $assignment;
    }

    private function overlappingAssignment(User $candidate, Event $event): ?Assignment
    {
        return $candidate->assignments->first(function (Assignment $assignment) use ($event): bool {
            $other = $assignment->event;

            return $other !== null
                && $other->id !== $event->id
                && $other->event_date->equalTo($event->event_date)
                && $other->meeting_time < $event->dismissal_time
                && $other->dismissal_time > $event->meeting_time;
        });
    }
}
