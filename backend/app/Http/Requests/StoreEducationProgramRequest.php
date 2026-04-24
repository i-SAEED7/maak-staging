<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreEducationProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:150', 'unique:education_programs,name_ar'],
            'code' => ['nullable', 'string', 'max:100', 'unique:education_programs,code'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
