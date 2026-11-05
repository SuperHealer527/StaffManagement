<?php

namespace App\Http\Requests;

use App\Models\SecureActionToken;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePublicActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->actionToken()?->isUsable() ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return match ($this->actionToken()?->purpose) {
            'assignment_response' => ['response' => ['required', 'in:accepted,unknown,declined']],
            'report_submission' => [
                'completion' => ['required', 'in:completed,issue,incident'],
                'attendance' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'notes' => ['nullable', 'string', 'max:5000'],
            ],
            'interview_selection' => ['selected_at' => ['required', 'date', 'after:now']],
            'temporary_profile' => [
                'postal_code' => ['required', 'string', 'max:12'],
                'address' => ['required', 'string', 'max:1000'],
                'bank_name' => ['required', 'string', 'max:100'],
                'branch_name' => ['required', 'string', 'max:100'],
                'account_type' => ['required', 'in:ordinary,current'],
                'account_number' => ['required', 'string', 'max:20'],
                'account_name' => ['required', 'string', 'max:100'],
            ],
            'first_password' => ['password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]],
            'attendance_confirmation', 'line_connect' => [],
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
