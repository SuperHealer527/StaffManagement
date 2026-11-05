<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
        return ['status' => ['required', 'in:requested,unknown,accepted,declined,confirmed,rejected,cancelled,absent,completed'], 'special_notes' => ['nullable', 'string', 'max:2000'], 'fee' => ['nullable', 'integer', 'min:0', 'max:1000000'], 'transportation' => ['nullable', 'integer', 'min:0', 'max:1000000']];
    }
}
