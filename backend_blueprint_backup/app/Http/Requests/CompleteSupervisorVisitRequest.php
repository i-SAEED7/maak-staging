<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class CompleteSupervisorVisitRequest
{
    public function rules(): array
    {
        return [
            'summary' => ['nullable', 'string'],
            'overall_score' => ['nullable', 'numeric'],
            'items' => ['required', 'array'],
        ];
    }
}
