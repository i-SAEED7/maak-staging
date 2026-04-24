<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:20480'],
            'category' => ['required', 'string', 'max:30'],
            'related_type' => ['nullable', 'string', 'max:100'],
            'related_id' => ['nullable', 'integer'],
            'is_sensitive' => ['nullable', 'boolean'],
            'visibility' => ['nullable', 'in:private,school,public'],
        ];
    }
}
