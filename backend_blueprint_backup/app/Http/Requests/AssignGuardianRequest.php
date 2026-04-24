<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class AssignGuardianRequest
{
    public function rules(): array
    {
        return [
            'parent_user_id' => ['required', 'integer'],
            'relationship' => ['required', 'string', 'max:30'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
