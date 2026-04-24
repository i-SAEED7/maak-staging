<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class AssignSupervisorRequest
{
    public function rules(): array
    {
        return [
            'supervisor_user_id' => ['required', 'integer'],
        ];
    }
}
