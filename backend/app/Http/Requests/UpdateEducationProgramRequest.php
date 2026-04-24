<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateEducationProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $programId = $this->route('educationProgram')?->id;

        return [
            'name_ar' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('education_programs', 'name_ar')->ignore($programId)],
            'code' => ['nullable', 'string', 'max:100', Rule::unique('education_programs', 'code')->ignore($programId)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
