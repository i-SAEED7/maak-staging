<?php

declare(strict_types=1);

namespace App\Listeners;

final class StoreAuditLogEntry
{
    public function handle(): void
    {
        // Persist audit trail entry for critical domain events.
    }
}
