<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class CommentIepPlanRequest
{
    public function rules(): array
    {
        return [
            'target_section' => ['nullable', 'string', 'max:100'],
            'comment_text' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}
