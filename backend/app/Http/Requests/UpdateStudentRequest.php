<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['sometimes', 'nullable', 'integer', 'exists:schools,id'],
            'academic_year_id' => ['sometimes', 'nullable', 'integer', 'exists:academic_years,id'],
            'education_program_id' => ['sometimes', 'nullable', 'integer', 'exists:education_programs,id'],
            'disability_category_id' => ['sometimes', 'nullable', 'integer', 'exists:disability_categories,id'],
            'primary_teacher_user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'father_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'grandfather_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'family_name' => ['sometimes', 'required', 'string', 'max:100'],
            'student_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'gender' => ['sometimes', 'required', Rule::in(['male', 'female'])],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'grade_level' => ['sometimes', 'nullable', 'string', 'max:50'],
            'classroom' => ['sometimes', 'nullable', 'string', 'max:50'],
            'medical_notes' => ['sometimes', 'nullable', 'array'],
            'social_notes' => ['sometimes', 'nullable', 'array'],
            'transportation_notes' => ['sometimes', 'nullable', 'string'],
            'joined_at' => ['sometimes', 'nullable', 'date'],
            'enrollment_status' => ['sometimes', 'nullable', Rule::in(['active', 'pending', 'archived', 'transferred', 'graduated'])],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
