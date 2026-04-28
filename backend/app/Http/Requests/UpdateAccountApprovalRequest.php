<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AccountApprovalRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateAccountApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'super_admin';
    }

    public function rules(): array
    {
        $approval = $this->route('accountApproval');
        $approvalId = $approval instanceof AccountApprovalRequest ? $approval->id : null;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'second_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                'unique:users,email',
                Rule::unique('account_approval_requests', 'email')
                    ->ignore($approvalId)
                    ->where(static fn ($query) => $query->where('status', 'pending')),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'account_type' => ['sometimes', 'required', 'string', Rule::in(['parent', 'teacher', 'principal'])],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                'unique:users,phone',
                Rule::unique('account_approval_requests', 'phone')
                    ->ignore($approvalId)
                    ->where(static fn ($query) => $query->where('status', 'pending')),
            ],
            'stage' => ['sometimes', 'required', 'string', Rule::in(['ابتدائي', 'متوسط', 'ثانوي', 'متعدد المراحل'])],
            'school_id' => ['sometimes', 'required', 'integer', 'exists:schools,id'],
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
