<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhoneLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageAssignments() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'called_at' => ['required', 'date'],
            'caller_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'purpose' => ['required', 'in:お仕事の依頼・相談,問い合わせ,スタッフからの連絡,営業・売り込み,その他'],
            'content' => ['required', 'string', 'max:10000'],
            'recipient_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->whereIn('role', ['employee', 'semi_employee'])
                    ->where('account_status', 'active')),
            ],
            'status' => ['required', 'in:callback_required,message_only,completed'],
        ];
    }
}
