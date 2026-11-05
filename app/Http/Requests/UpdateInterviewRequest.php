<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
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
            'type' => ['required', 'in:regular,individual_guidance,evaluation,other'],
            'method' => ['required', 'in:in_person,online,phone'],
            'location' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'content' => ['nullable', 'string', 'max:10000'],
            'next_tasks' => ['nullable', 'string', 'max:5000'],
            'next_timing' => ['nullable', 'string', 'max:255'],
        ];
    }
}
