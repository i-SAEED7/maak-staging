<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAccountApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'second_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
                Rule::unique('account_approval_requests', 'email')
                    ->where(static fn ($query) => $query->where('status', 'pending')),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', 'string', Rule::in(['parent', 'teacher', 'principal'])],
            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone',
                Rule::unique('account_approval_requests', 'phone')
                    ->where(static fn ($query) => $query->where('status', 'pending')),
            ],
            'stage' => ['required', 'string', Rule::in(['ابتدائي', 'متوسط', 'ثانوي', 'متعدد المراحل'])],
            'school_id' => ['required', 'integer', 'exists:schools,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'رقم الجوال أو البريد الإلكتروني مسجل مسبقًا.',
            'phone.unique' => 'رقم الجوال أو البريد الإلكتروني مسجل مسبقًا.',
        ];
    }
}
