<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class StudentResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'uuid' => null,
            'full_name' => null,
            'school_id' => null,
            'enrollment_status' => null,
        ];
    }
}
