<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class IepPlanResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'uuid' => null,
            'student_id' => null,
            'status' => null,
            'current_version_number' => null,
            'title' => null,
        ];
    }
}
