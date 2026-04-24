<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'target_audience' => ['required', 'string', Rule::in(['teacher', 'principal', 'supervisor', 'parent', 'general'])],
            'is_all_schools' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }
}
