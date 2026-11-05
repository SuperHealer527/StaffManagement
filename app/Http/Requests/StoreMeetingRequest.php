<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
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
            'type' => ['required', 'in:sales,preparation,consultation,follow_up,other'],
            'meeting_date' => ['required', 'date'],
            'method' => ['required', 'in:visit,phone,online,email,other'],
            'participants' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:10000'],
        ];
    }
}
