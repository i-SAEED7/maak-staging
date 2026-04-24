<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSupervisorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['nullable', 'integer', 'exists:supervision_templates,id'],
            'visit_date' => ['sometimes', 'date'],
            'agenda' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
            'visit_status' => ['nullable', 'in:scheduled,in_progress,completed,cancelled'],
        ];
    }
}
