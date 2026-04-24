<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreUserRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'max:50'],
            'school_id' => ['nullable', 'integer'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'must_change_password' => ['nullable', 'boolean'],
        ];
    }
}
