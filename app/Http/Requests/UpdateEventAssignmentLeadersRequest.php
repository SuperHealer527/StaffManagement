<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventAssignmentLeadersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageAssignments() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day_leader_assignment_id' => ['nullable', 'integer', 'exists:assignments,id'],
            'report_representative_assignment_id' => ['nullable', 'integer', 'exists:assignments,id'],
            'representative_same_as_leader' => ['nullable', 'boolean'],
        ];
    }
}
