<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'school_ids' => ['nullable', 'array'],
            'school_ids.*' => ['integer', 'exists:schools,id', 'distinct'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'must_change_password' => ['nullable', 'boolean'],
        ];
    }
}
