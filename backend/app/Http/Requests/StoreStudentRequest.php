<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'education_program_id' => ['nullable', 'integer', 'exists:education_programs,id'],
            'disability_category_id' => ['nullable', 'integer', 'exists:disability_categories,id'],
            'primary_teacher_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'grandfather_name' => ['nullable', 'string', 'max:100'],
            'family_name' => ['required', 'string', 'max:100'],
            'student_number' => ['nullable', 'string', 'max:50'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'classroom' => ['nullable', 'string', 'max:50'],
            'medical_notes' => ['nullable', 'array'],
            'social_notes' => ['nullable', 'array'],
            'transportation_notes' => ['nullable', 'string'],
            'joined_at' => ['nullable', 'date'],
            'enrollment_status' => ['nullable', Rule::in(['active', 'pending', 'archived', 'transferred', 'graduated'])],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
