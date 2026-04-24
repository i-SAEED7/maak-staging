<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apply_to_all' => ['nullable', 'boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')],
            'permission_keys' => ['required', 'array'],
            'permission_keys.*' => ['string', Rule::exists('permissions', 'key')],
        ];
    }
}
