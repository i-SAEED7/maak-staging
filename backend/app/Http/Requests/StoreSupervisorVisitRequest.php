<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreSupervisorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'template_id' => ['nullable', 'integer', 'exists:supervision_templates,id'],
            'visit_date' => ['required', 'date'],
            'agenda' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }
}
