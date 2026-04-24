<?php

declare(strict_types=1);

namespace App\Listeners;

final class QueueIepPdfGeneration
{
    public function handle(): void
    {
        // Dispatch GenerateIepPdfJob after approval.
    }
}
