<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StorePortfolioRequest
{
    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer'],
            'owner_user_id' => ['nullable', 'integer'],
            'student_id' => ['nullable', 'integer'],
            'type' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
