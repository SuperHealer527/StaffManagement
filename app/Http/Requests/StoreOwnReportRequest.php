<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOwnReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $assignment !== null
            && $this->user()?->id === $assignment->user_id
            && in_array($assignment->status, ['confirmed', 'completed'], true)
            && $assignment->is_report_representative
            && $assignment->event()
                ->where('report_required', true)
                ->whereBetween('event_date', [today()->subDays(7), today()])
                ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'completion' => ['required', 'in:completed,issue,incident'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
