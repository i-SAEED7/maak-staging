<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class ChangeSchoolStatusRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:20'],
        ];
    }
}
