<?php

declare(strict_types=1);

namespace App\Observers;

final class StudentObserver
{
    public function creating(): void
    {
        // Normalize student full name before persistence.
    }

    public function updating(): void
    {
        // Rebuild search-friendly full name when name fields change.
    }
}
