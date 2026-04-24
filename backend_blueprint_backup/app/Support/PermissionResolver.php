<?php

declare(strict_types=1);

namespace App\Support;

final class PermissionResolver
{
    public function resolveForUser(): array
    {
        // Resolve effective permissions from role plus contextual constraints.
        return [];
    }
}
