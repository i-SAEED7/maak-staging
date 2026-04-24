<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompleteSupervisorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary' => ['nullable', 'string'],
            'overall_score' => ['nullable', 'numeric'],
            'items' => ['required', 'array'],
            'items.*.criterion_key' => ['required', 'string', 'max:100'],
            'items.*.criterion_label' => ['required', 'string', 'max:255'],
            'items.*.score' => ['nullable', 'numeric'],
            'items.*.remarks' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }
}
