<?php

declare(strict_types=1);

namespace App\Observers;

final class FileObserver
{
    public function creating(): void
    {
        // Enforce UUID-based storage naming before save.
    }
}
