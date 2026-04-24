<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'role' => ['sometimes', 'string', Rule::exists('roles', 'name')],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'school_ids' => ['nullable', 'array'],
            'school_ids.*' => ['integer', 'exists:schools,id', 'distinct'],
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($userId)],
            'national_id' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
            'must_change_password' => ['nullable', 'boolean'],
        ];
    }
}
