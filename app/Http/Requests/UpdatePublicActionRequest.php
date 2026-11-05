<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use App\Models\SecureActionToken;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePublicActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actionToken = $this->actionToken();
        if (! ($actionToken?->isUsable() ?? false)) {
            return false;
        }

        return $actionToken->purpose !== 'assignment_response'
            || ($actionToken->actionable instanceof Assignment
                && in_array($actionToken->actionable->status, ['requested', 'unknown', 'accepted'], true));
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return match ($this->actionToken()?->purpose) {
            'assignment_response' => ['response' => ['required', 'in:accepted,unknown,declined']],
            'report_submission' => [
                'completion' => ['required', 'in:completed,issue,incident'],
                'answers' => ['nullable', 'array'],
                'answers.*' => ['nullable', 'string', 'max:5000'],
            ],
            'interview_selection' => ['selected_at' => ['required', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === 'unavailable') {
                    return;
                }
                if (! is_string($value) || strtotime($value) === false || strtotime($value) <= now()->timestamp) {
                    $fail('未来の候補日時を選択してください。');
                }
            }]],
            'temporary_profile' => [
                'postal_code' => ['required', 'string', 'max:12'],
                'address' => ['required', 'string', 'max:1000'],
                'building' => ['nullable', 'string', 'max:255'],
                'bank_name' => ['required', 'string', 'max:100'],
                'bank_code' => ['nullable', 'string', 'max:10'],
                'branch_name' => ['required', 'string', 'max:100'],
                'branch_code' => ['nullable', 'string', 'max:10'],
                'account_type' => ['required', 'in:ordinary,current'],
                'account_number' => ['required', 'string', 'max:20'],
                'account_name' => ['required', 'string', 'max:100'],
            ],
            'first_password' => ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]],
            'attendance_confirmation', 'attendance_seven_day', 'attendance_same_day', 'line_connect' => [],
            default => ['action' => ['required', 'prohibited']],
        };
    }

    public function actionToken(): ?SecureActionToken
    {
        if (! $this->route('token')) {
            return null;
        }

        return SecureActionToken::query()
            ->with(['user', 'actionable'])
            ->where('token_hash', hash('sha256', (string) $this->route('token')))
            ->first();
    }
}
