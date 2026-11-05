<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DuplicateEventRequest extends FormRequest
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
            'dates' => ['required', 'array', 'min:1', 'max:52'],
            'dates.*' => ['required', 'date', 'distinct'],
            'status' => ['required', 'in:draft,recruiting'],
            'copy_name' => ['nullable', 'boolean'],
            'copy_schedule' => ['nullable', 'boolean'],
            'copy_location' => ['nullable', 'boolean'],
            'copy_details' => ['nullable', 'boolean'],
            'copy_report' => ['nullable', 'boolean'],
        ];
    }
}
