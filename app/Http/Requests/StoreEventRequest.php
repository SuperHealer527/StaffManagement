<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
        return ['client_id' => ['required', 'exists:clients,id'], 'business_name' => ['required', 'string', 'max:255'], 'name' => ['nullable', 'string', 'max:255'], 'details' => ['nullable', 'string', 'max:5000'], 'work_content' => ['nullable', 'string', 'max:5000'], 'event_date' => ['required', 'date'], 'meeting_time' => ['required', 'date_format:H:i'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'dismissal_time' => ['required', 'date_format:H:i', 'after_or_equal:end_time'], 'required_staff' => ['required', 'integer', 'min:1', 'max:100'], 'meeting_place' => ['nullable', 'string', 'max:255'], 'venue' => ['nullable', 'string', 'max:255'], 'items' => ['nullable', 'array'], 'items.*' => ['string', 'max:255'], 'emergency_contacts' => ['nullable', 'array'], 'emergency_contacts.*' => ['string', 'max:255'], 'notes' => ['nullable', 'string', 'max:5000'], 'status' => ['required', 'in:draft,recruiting'], 'report_required' => ['required', 'boolean']];
    }
}
