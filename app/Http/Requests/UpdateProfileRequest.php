<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'is_student' => ['nullable', 'boolean'],
            'nearest_station' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:12'],
            'address' => ['nullable', 'string', 'max:1000'],
            'building' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()?->id)],
            'secondary_email' => ['nullable', 'email', 'max:255'],
            'notification_method' => ['required', 'in:email,line'],
            'has_car' => ['nullable', 'boolean'],
            'car_number' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'required_with:account_number', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:10'],
            'branch_name' => ['nullable', 'required_with:account_number', 'string', 'max:100'],
            'branch_code' => ['nullable', 'string', 'max:10'],
            'account_type' => ['nullable', 'required_with:account_number', 'in:ordinary,current'],
            'account_number' => ['nullable', 'string', 'max:20'],
            'account_name' => ['nullable', 'required_with:account_number', 'string', 'max:100'],
            'availabilities' => ['nullable', 'array'],
            'availabilities.*' => ['required', 'in:available,unknown,unavailable'],
        ];
    }
}
