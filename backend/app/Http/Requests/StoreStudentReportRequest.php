<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreStudentReportRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer'],
            'report_type' => ['required', 'string', 'max:30'],
            'report_period_label' => ['required', 'string', 'max:100'],
            'content_json' => ['required', 'array'],
            'summary' => ['nullable', 'string'],
        ];
    }
}
