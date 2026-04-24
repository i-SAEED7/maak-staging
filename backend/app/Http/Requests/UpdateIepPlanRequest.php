<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateIepPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'required', 'integer', 'exists:students,id'],
            'academic_year_id' => ['sometimes', 'nullable', 'integer', 'exists:academic_years,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'strengths' => ['sometimes', 'nullable', 'string'],
            'needs' => ['sometimes', 'nullable', 'string'],
            'accommodations' => ['sometimes', 'nullable', 'array'],
            'goals' => ['sometimes', 'nullable', 'array'],
            'goals.*.domain' => ['required_with:goals', 'string', 'max:100'],
            'goals.*.goal_text' => ['required_with:goals', 'string'],
            'goals.*.measurement_method' => ['required_with:goals', 'string'],
            'goals.*.baseline_value' => ['nullable', 'string', 'max:100'],
            'goals.*.target_value' => ['nullable', 'string', 'max:100'],
            'goals.*.due_date' => ['nullable', 'date'],
            'goals.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
