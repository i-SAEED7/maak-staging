<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class ArchiveStudentRequest
{
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string'],
        ];
    }
}
