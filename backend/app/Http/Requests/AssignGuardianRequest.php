<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_user_id' => ['required', 'integer', 'exists:users,id'],
            'relationship' => ['required', 'string', 'max:30'],
            'is_primary' => ['nullable', 'boolean'],
            'can_view_reports' => ['nullable', 'boolean'],
            'can_message_school' => ['nullable', 'boolean'],
        ];
    }
}
