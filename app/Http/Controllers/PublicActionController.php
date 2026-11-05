<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePublicActionRequest;
use App\Models\Assignment;
use App\Models\Interview;
use App\Models\ReportSubmission;
use App\Models\SecureActionToken;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PublicActionController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function show(string $token): View
    {
        $actionToken = $this->findUsableToken($token);
        if ($actionToken->opened_at === null) {
            $actionToken->update(['opened_at' => now()]);
        }

        $viewData = $this->viewData($actionToken);
        $template = match ($viewData['actionToken']->purpose) {
            'assignment_response' => 'static-pages.Respond',
            'report_submission' => 'static-pages.Report',
            'interview_selection' => 'static-pages.InterviewSchedule',
            'temporary_profile' => 'static-pages.PaymentDetails',
            'first_password' => 'static-pages.SetPassword',
            'line_connect' => 'static-pages.LineConnect',
            default => 'public-actions.show',
        };

        return view($template, $viewData);
    }

    public function update(UpdatePublicActionRequest $request, string $token): RedirectResponse
    {
        $actionToken = $request->actionToken();
        abort_unless($actionToken?->isUsable(), 410);

        DB::transaction(function () use ($request, $actionToken): void {
            $validated = $request->validated();

            match ($actionToken->purpose) {
                'assignment_response' => $this->recordAssignmentResponse($actionToken, $validated['response']),
                'attendance_confirmation', 'attendance_seven_day', 'attendance_same_day' => $this->recordAttendance($actionToken),
                'report_submission' => $this->recordReport($actionToken, $validated),
                'interview_selection' => $this->recordInterview($actionToken, $validated['selected_at']),
                'temporary_profile' => $this->recordTemporaryProfile($actionToken, $validated),
                'first_password' => $this->recordPassword($actionToken, $validated['password']),
                'line_connect' => null,
                default => abort(422),
            };

            if (! in_array($actionToken->purpose, ['line_connect', 'assignment_response'], true)) {
                $actionToken->update(['used_at' => now()]);
            }
        });

        if ($actionToken->purpose === 'first_password' && $actionToken->user !== null) {
            Auth::login($actionToken->user);
            $request->session()->regenerate();

            return redirect()->route('screens.show', 'profile')->with('status', 'パスワードを設定しました。プロフィールを確認してください。');
        }

        return redirect()->route('public-actions.complete', $token)->with('status', $actionToken->purpose === 'line_connect'
            ? 'LINE連携にはMessaging APIの設定が必要です。'
            : '送信しました。ありがとうございます。');
    }

    public function complete(string $token): View
    {
        $actionToken = SecureActionToken::query()
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();

        if (in_array($actionToken->purpose, ['attendance_confirmation', 'attendance_seven_day', 'attendance_same_day'], true)) {
            return view('static-pages.AttendanceComplete', ['actionToken' => $actionToken]);
        }

        return view('public-actions.complete', ['actionToken' => $actionToken]);
    }

    private function findUsableToken(string $token): SecureActionToken
    {
        $actionToken = SecureActionToken::query()
            ->with(['user', 'actionable'])
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();

        abort_unless($actionToken->isUsable(), 410);

        return $actionToken;
    }

    /** @return array<string, mixed> */
    private function viewData(SecureActionToken $actionToken): array
    {
        $assignment = $actionToken->actionable instanceof Assignment ? $actionToken->actionable->load(['event.client', 'event.reportQuestions']) : null;
        $interview = $actionToken->actionable instanceof Interview ? $actionToken->actionable->load(['subject', 'manager']) : null;

        return [
            'actionToken' => $actionToken,
            'assignment' => $assignment,
            'event' => $assignment?->event,
            'interview' => $interview,
            'user' => $actionToken->user,
            'reportSubmission' => $assignment === null ? null : ReportSubmission::query()->where('event_id', $assignment->event_id)->where('user_id', $assignment->user_id)->first(),
        ];
    }

    private function recordAssignmentResponse(SecureActionToken $actionToken, string $response): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        abort_unless(in_array($actionToken->actionable->status, ['requested', 'unknown', 'accepted'], true), 422);
        $oldStatus = $actionToken->actionable->status;
        $actionToken->actionable->update(['status' => $response, 'responded_at' => now()]);
        $this->auditService->record($actionToken->actionable, 'public_assignment_response', ['status' => $oldStatus], ['status' => $response]);
    }

    private function recordAttendance(SecureActionToken $actionToken): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        $field = $actionToken->purpose === 'attendance_seven_day'
            ? 'seven_day_confirmation'
            : 'same_day_confirmation';
        $actionToken->actionable->update([$field => 'confirmed']);
        $this->auditService->record($actionToken->actionable, 'public_attendance_confirmation', null, [$field => 'confirmed']);
    }

    /** @param array<string, mixed> $values */
    private function recordReport(SecureActionToken $actionToken, array $values): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        $submission = ReportSubmission::query()->updateOrCreate(
            ['event_id' => $actionToken->actionable->event_id, 'user_id' => $actionToken->actionable->user_id],
            ['answers' => ['completion' => $values['completion']] + ($values['answers'] ?? []), 'submitted_at' => now()],
        );
        $this->auditService->record($submission, 'public_report_submission');
    }

    private function recordInterview(SecureActionToken $actionToken, string $selectedAt): void
    {
        abort_unless($actionToken->actionable instanceof Interview, 422);
        if ($selectedAt === 'unavailable') {
            $actionToken->actionable->update(['candidate_times' => [], 'scheduled_at' => null]);
            $this->auditService->record($actionToken->actionable, 'public_interview_response', null, ['selected_at' => 'unavailable']);

            return;
        }
        $allowed = collect($actionToken->actionable->candidate_times)->contains(
            fn (string $candidate): bool => Carbon::parse($candidate)->equalTo(Carbon::parse($selectedAt)),
        );
        abort_unless($allowed, 422);
        $actionToken->actionable->update(['scheduled_at' => $selectedAt]);
        $this->auditService->record($actionToken->actionable, 'public_interview_response', null, ['selected_at' => $selectedAt]);
    }

    /** @param array<string, mixed> $values */
    private function recordTemporaryProfile(SecureActionToken $actionToken, array $values): void
    {
        abort_unless($actionToken->user instanceof User, 422);
        $actionToken->user->update([
            'postal_code' => $values['postal_code'],
            'address' => $values['address'],
            'building' => $values['building'] ?? null,
            'bank_details' => collect($values)->only(['bank_name', 'bank_code', 'branch_name', 'branch_code', 'account_type', 'account_number', 'account_name'])->all(),
        ]);
        $this->auditService->record($actionToken->user, 'public_temporary_profile_submission');
    }

    private function recordPassword(SecureActionToken $actionToken, string $password): void
    {
        abort_unless($actionToken->user instanceof User, 422);
        abort_if($actionToken->user->role === 'temporary', 422);
        $actionToken->user->update(['password' => Hash::make($password), 'account_status' => 'active']);
        $this->auditService->record($actionToken->user, 'public_first_password_setup');
    }
}
