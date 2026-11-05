<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
        return ['name' => ['required', 'string', 'max:255'], 'type' => ['required', 'in:corporation,government,individual,organization,other'], 'department' => ['nullable', 'string', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'postal_code' => ['nullable', 'string', 'max:12'], 'address' => ['nullable', 'string', 'max:1000'], 'notes' => ['nullable', 'string', 'max:5000']];
    }
}
