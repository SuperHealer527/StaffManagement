<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Models\AuditLog;
use App\Models\Interview;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationDeliveryService;
use App\Services\SecureActionLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InterviewController extends Controller
{
    public function __construct(
        private SecureActionLinkService $secureActionLinkService,
        private AuditService $auditService,
        private NotificationDeliveryService $notificationDeliveryService,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isEmployee(), 403);
        $interviews = Interview::query()
            ->with(['subject', 'manager'])
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_user_id', $request->integer('subject_id')))
            ->when($request->input('status') === 'pending', fn ($query) => $query->whereNull('scheduled_at'))
            ->when($request->input('status') === 'scheduled', fn ($query) => $query->whereNotNull('scheduled_at'))
            ->latest()
            ->get();
        $selectedInterview = $interviews->firstWhere('id', $request->integer('interview_id')) ?? $interviews->first();
        if ($selectedInterview) {
            $this->auditService->record($selectedInterview, 'view_interview_record');
        }
        $auditAccessLogs = $selectedInterview === null
            ? collect()
            : AuditLog::query()
                ->with('user')
                ->where('auditable_type', $selectedInterview->getMorphClass())
                ->where('auditable_id', $selectedInterview->id)
                ->where('action', 'view_interview_record')
                ->latest()
                ->limit(20)
                ->get();
        $staffMembers = User::query()
            ->whereIn('role', ['semi_employee', 'staff', 'trainer', 'temporary'])
            ->where(fn ($query) => $query->where('account_status', 'active')->orWhere(fn ($temporaryQuery) => $temporaryQuery->where('role', 'temporary')->where('account_status', 'no_login')))
            ->orderBy('name')
            ->get();
        $managers = User::query()->where('role', 'employee')->where('account_status', 'active')->orderBy('name')->get();

        return view('static-pages.Coaching', compact('interviews', 'selectedInterview', 'staffMembers', 'managers', 'auditAccessLogs'));
    }

    public function store(StoreInterviewRequest $request): RedirectResponse
    {
        $interview = DB::transaction(function () use ($request): Interview {
            $interview = Interview::query()->create($request->validated());
            $this->auditService->record($interview, 'create_interview', null, $interview->only(['subject_user_id', 'manager_id', 'type', 'method', 'candidate_times']));

            return $interview->load(['subject', 'manager']);
        });
        $this->sendInvitation($interview);

        return redirect()->route('interviews.index', ['interview_id' => $interview->id])->with('status', '面談候補日時を送信しました。');
    }

    public function update(UpdateInterviewRequest $request, Interview $interview): RedirectResponse
    {
        $oldValues = $interview->only(['type', 'method', 'location', 'scheduled_at', 'content', 'next_tasks', 'next_timing']);
        $interview->update($request->validated());
        $this->auditService->record($interview, 'update_interview', $oldValues, $interview->only(array_keys($oldValues)));

        return back()->with('status', '面談予定・記録を更新しました。');
    }

    private function sendInvitation(Interview $interview): void
    {
        $token = $this->secureActionLinkService->create($interview->subject, 'interview_selection', $interview, 24);
        $url = route('public-actions.show', $token);
        $candidateText = collect($interview->candidate_times)
            ->map(fn (string $candidate): string => Carbon::parse($candidate)->isoFormat('M月D日(ddd) H:mm'))
            ->join('／');
        $this->notificationDeliveryService->deliver(
            $interview->subject,
            $interview,
            'interview_candidates',
            '面談の日程のご案内',
            sprintf('%sとの%sの日程を選んでください。候補：%s', $interview->manager->name, $interview->type, $candidateText),
            $url,
            '日程を選ぶ',
        );
    }
}
