<?php

declare(strict_types=1);

namespace App\Support;

final class TemporaryFileAccessService
{
    public function issue(): void
    {
        // Create temporary token for file access.
    }

    public function consume(): void
    {
        // Mark token consumed when used.
    }
}
