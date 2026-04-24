<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class SchoolResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'uuid' => null,
            'name_ar' => null,
            'region' => null,
            'city' => null,
            'status' => null,
        ];
    }
}
