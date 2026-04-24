<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class TransitionIepPlanRequest
{
    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
        ];
    }
}
