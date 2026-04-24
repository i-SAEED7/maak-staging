<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class UserResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'uuid' => null,
            'full_name' => null,
            'role' => null,
            'school_id' => null,
            'status' => null,
        ];
    }
}
