<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Contracts\ExternalStudentProvider;

/**
 * No-operation adapter used in Phase 1 before any external system is connected.
 *
 * All lookups return empty/not-found results. This ensures the application
 * code is already wired to use the ExternalStudentProvider contract, making
 * future integration with Noor or Fares seamless.
 */
final class NullExternalAdapter implements ExternalStudentProvider
{
    public function findByNationalId(string $nationalId): array
    {
        return [
            'found' => false,
            'source' => $this->providerName(),
        ];
    }

    public function syncEnrollmentStatus(string $nationalId): array
    {
        return [
            'synced' => false,
            'message' => 'لا يوجد مزود خارجي مُفعّل حالياً',
        ];
    }

    public function healthCheck(): bool
    {
        return true; // Null adapter is always "healthy"
    }

    public function providerName(): string
    {
        return 'null';
    }
}
