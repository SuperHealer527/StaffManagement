<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUiActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user() === null) {
            return false;
        }

        if ($this->route('action') === 'availability') {
            return true;
        }

        if (in_array($this->route('action'), ['phone-log-create', 'phone-log-complete', 'assignment-status'], true)) {
            return $this->user()->canManageAssignments();
        }

        return $this->user()->isEmployee();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->route('action')) {
            'account-create' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', 'in:employee,semi_employee,staff,temporary,trainer'],
            ],
            'phone-log-create' => [
                'called_at' => ['required', 'date'],
                'caller_name' => ['required', 'string', 'max:255'],
                'company' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'purpose' => ['required', 'string', 'max:255'],
                'content' => ['required', 'string', 'max:5000'],
                'recipient_id' => ['nullable', 'integer', 'exists:users,id'],
                'status' => ['required', 'in:callback_required,message_only,completed'],
            ],
            'phone-log-complete' => ['phone_log_id' => ['required', 'integer', 'exists:phone_logs,id']],
            'account-status' => [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'account_status' => ['required', 'in:active,invited,suspended,no_login'],
            ],
            'account-role' => [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'role' => ['required', 'in:employee,semi_employee,staff,temporary,trainer'],
            ],
            'notification-retry' => ['notification_log_id' => ['required', 'integer', 'exists:notification_logs,id']],
            'availability' => [
                'weekday' => ['required', 'integer', 'between:0,6'],
                'time_slot' => ['required', 'string', 'max:50'],
                'availability' => ['required', 'in:available,unknown,unavailable'],
            ],
            'assignment-status' => [
                'assignment_id' => ['required', 'integer', 'exists:assignments,id'],
                'status' => ['required', 'in:requested,accepted,unknown,declined,confirmed,cancelled'],
            ],
            'payment-paid' => [
                'assignment_ids' => ['required', 'array', 'min:1'],
                'assignment_ids.*' => ['integer', 'exists:assignments,id'],
            ],
            default => ['action' => ['required', 'prohibited']],
        };
    }
}
