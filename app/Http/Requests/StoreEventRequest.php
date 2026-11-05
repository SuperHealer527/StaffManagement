<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $event = $this->route('event');
        $allowedStatuses = $event instanceof Event && $event->status === 'confirmed'
            ? ['draft', 'recruiting', 'confirmed']
            : ['draft', 'recruiting'];

        return [
            'client_id' => ['required', 'exists:clients,id'],
            'business_name' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:5000'],
            'work_content' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['required', 'date'],
            'meeting_time' => ['required', 'date_format:H:i'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'dismissal_time' => ['required', 'date_format:H:i', 'after_or_equal:end_time'],
            'required_staff' => ['required', 'integer', 'min:1', 'max:100'],
            'meeting_place' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*' => ['string', 'in:lunch,dinner,indoor_shoes,highclass_shirt,other'],
            'other_item' => ['nullable', 'string', 'max:255'],
            'emergency_contacts' => ['nullable', 'array'],
            'emergency_contacts.line' => ['nullable', 'boolean'],
            'emergency_contacts.employee_id' => ['nullable', 'integer', 'exists:users,id'],
            'emergency_contacts.leader' => ['nullable', 'boolean'],
            'emergency_contacts.other' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in($allowedStatuses)],
            'report_required' => ['required', 'boolean'],
            'report_questions' => ['nullable', 'array'],
            'report_questions.*.id' => ['nullable', 'integer', 'exists:report_questions,id'],
            'report_questions.*.type' => ['required', 'in:select,text'],
            'report_questions.*.question' => ['required', 'string', 'max:1000'],
            'report_questions.*.options' => ['nullable', 'array'],
            'report_questions.*.options.*' => ['required', 'string', 'max:255'],
            'invoice_items' => ['nullable', 'array'],
            'invoice_items.*.id' => ['nullable', 'integer', 'exists:invoice_items,id'],
            'invoice_items.*.type' => ['required', 'in:work,transportation,fuel,other'],
            'invoice_items.*.memo' => ['nullable', 'string', 'max:255'],
            'invoice_items.*.amount' => ['required', 'integer', 'min:0', 'max:100000000'],
            'expenses' => ['nullable', 'array'],
            'expenses.*.id' => ['nullable', 'integer', 'exists:expenses,id'],
            'expenses.*.type' => ['required', 'in:fuel,transportation,supplies,other'],
            'expenses.*.memo' => ['nullable', 'string', 'max:255'],
            'expenses.*.amount' => ['required', 'integer', 'min:0', 'max:100000000'],
            'expenses.*.receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }
}
