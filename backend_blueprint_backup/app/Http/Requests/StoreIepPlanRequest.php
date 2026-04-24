<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreIepPlanRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer'],
            'academic_year_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'needs' => ['nullable', 'string'],
            'accommodations' => ['nullable', 'array'],
            'goals' => ['nullable', 'array'],
        ];
    }
}
