<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreIepPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'summary' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'needs' => ['nullable', 'string'],
            'accommodations' => ['nullable', 'array'],
            'goals' => ['nullable', 'array'],
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
