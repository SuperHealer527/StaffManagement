<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'payment_status' => ['required', 'in:unconfirmed,waiting,paid'],
            'payment_source' => ['required_if:payment_status,paid', 'nullable', 'in:rakuten,gmo,yucho,cash'],
            'paid_at' => ['required_if:payment_status,paid', 'nullable', 'date'],
            'fee' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'transportation' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'reimbursement' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'withholding_tax' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ];
    }
}
