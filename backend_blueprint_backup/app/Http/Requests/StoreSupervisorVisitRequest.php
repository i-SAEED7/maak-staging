<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreSupervisorVisitRequest
{
    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer'],
            'template_id' => ['nullable', 'integer'],
            'visit_date' => ['required', 'date'],
            'agenda' => ['nullable', 'string'],
        ];
    }
}
