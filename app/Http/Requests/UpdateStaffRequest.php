<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
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
        $staff = $this->route('staff');
        $allowedStatuses = $staff?->role === 'temporary'
            ? ['no_login', 'suspended']
            : ['invited', 'active', 'suspended'];

        return [
            'base_fee' => ['required', 'integer', 'min:0', 'max:1000000'],
            'account_status' => ['required', Rule::in($allowedStatuses)],
        ];
    }
}
