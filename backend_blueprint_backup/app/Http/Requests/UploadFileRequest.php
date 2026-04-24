<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UploadFileRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required'],
            'category' => ['required', 'string', 'max:30'],
            'related_type' => ['nullable', 'string', 'max:100'],
            'related_id' => ['nullable', 'integer'],
            'is_sensitive' => ['nullable', 'boolean'],
        ];
    }
}
