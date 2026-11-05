<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInterviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isEmployee() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_user_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['semi_employee', 'staff', 'trainer', 'temporary'])->whereIn('account_status', ['active', 'no_login']))],
            'manager_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'employee')->where('account_status', 'active'))],
            'type' => ['required', 'in:regular,individual_guidance,evaluation,other'],
            'method' => ['required', 'in:in_person,online,phone'],
            'location' => ['nullable', 'string', 'max:255'],
            'candidate_times' => ['required', 'array', 'min:1', 'max:3'],
            'candidate_times.*' => ['required', 'date', 'after:now', 'distinct'],
        ];
    }
}
