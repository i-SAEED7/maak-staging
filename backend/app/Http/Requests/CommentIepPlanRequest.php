<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CommentIepPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_section' => ['nullable', 'string', 'max:100'],
            'comment_text' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}
