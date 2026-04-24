<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class LoginRequest
{
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
