<?php

declare(strict_types=1);

namespace App\Observers;

final class IepPlanObserver
{
    public function created(): void
    {
        // Create first version snapshot after initial draft creation.
    }

    public function updated(): void
    {
        // Persist version snapshot for material changes.
    }
}
