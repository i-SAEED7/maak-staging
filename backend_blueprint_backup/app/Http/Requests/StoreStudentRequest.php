<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreStudentRequest
{
    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer'],
            'education_program_id' => ['nullable', 'integer'],
            'disability_category_id' => ['nullable', 'integer'],
            'primary_teacher_user_id' => ['nullable', 'integer'],
            'first_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'family_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'max:10'],
            'birth_date' => ['nullable', 'date'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'classroom' => ['nullable', 'string', 'max:50'],
            'student_number' => ['nullable', 'string', 'max:50'],
            'medical_notes' => ['nullable', 'array'],
        ];
    }
}
