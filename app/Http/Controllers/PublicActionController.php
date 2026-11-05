<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePublicActionRequest;
use App\Models\Assignment;
use App\Models\Interview;
use App\Models\ReportSubmission;
use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PublicActionController extends Controller
{
    public function show(string $token): View
    {
        $viewData = $this->viewData($this->findUsableToken($token));
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
                'attendance_confirmation' => $this->recordAttendance($actionToken),
                'report_submission' => $this->recordReport($actionToken, $validated),
                'interview_selection' => $this->recordInterview($actionToken, $validated['selected_at']),
                'temporary_profile' => $this->recordTemporaryProfile($actionToken, $validated),
                'first_password' => $this->recordPassword($actionToken, $validated['password']),
                'line_connect' => null,
                default => abort(422),
            };

            if ($actionToken->purpose !== 'line_connect') {
                $actionToken->update(['used_at' => now()]);
            }
        });

        return redirect()->route('public-actions.complete', $token)->with('status', $actionToken->purpose === 'line_connect'
            ? 'LINE連携にはMessaging APIの設定が必要です。'
            : '送信しました。ありがとうございます。');
    }

    public function complete(string $token): View
    {
        $actionToken = SecureActionToken::query()
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();

        if ($actionToken->purpose === 'attendance_confirmation') {
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
        $assignment = $actionToken->actionable instanceof Assignment ? $actionToken->actionable->load('event.client') : null;
        $interview = $actionToken->actionable instanceof Interview ? $actionToken->actionable->load(['subject', 'manager']) : null;

        return [
            'actionToken' => $actionToken,
            'assignment' => $assignment,
            'event' => $assignment?->event,
            'interview' => $interview,
            'user' => $actionToken->user,
        ];
    }

    private function recordAssignmentResponse(SecureActionToken $actionToken, string $response): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        $actionToken->actionable->update(['status' => $response, 'responded_at' => now()]);
    }

    private function recordAttendance(SecureActionToken $actionToken): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        $actionToken->actionable->update(['same_day_confirmation' => 'confirmed']);
    }

    /** @param array<string, mixed> $values */
    private function recordReport(SecureActionToken $actionToken, array $values): void
    {
        abort_unless($actionToken->actionable instanceof Assignment, 422);
        ReportSubmission::query()->updateOrCreate(
            ['event_id' => $actionToken->actionable->event_id, 'user_id' => $actionToken->actionable->user_id],
            ['answers' => $values, 'submitted_at' => now()],
        );
    }

    private function recordInterview(SecureActionToken $actionToken, string $selectedAt): void
    {
        abort_unless($actionToken->actionable instanceof Interview, 422);
        $allowed = collect($actionToken->actionable->candidate_times)->contains(
            fn (string $candidate): bool => Carbon::parse($candidate)->equalTo(Carbon::parse($selectedAt)),
        );
        abort_unless($allowed, 422);
        $actionToken->actionable->update(['scheduled_at' => $selectedAt]);
    }

    /** @param array<string, mixed> $values */
    private function recordTemporaryProfile(SecureActionToken $actionToken, array $values): void
    {
        abort_unless($actionToken->user instanceof User, 422);
        $actionToken->user->update([
            'postal_code' => $values['postal_code'],
            'address' => $values['address'],
            'bank_details' => collect($values)->only(['bank_name', 'branch_name', 'account_type', 'account_number', 'account_name'])->all(),
        ]);
    }

    private function recordPassword(SecureActionToken $actionToken, string $password): void
    {
        abort_unless($actionToken->user instanceof User, 422);
        $actionToken->user->update(['password' => Hash::make($password), 'account_status' => 'active']);
    }
}
