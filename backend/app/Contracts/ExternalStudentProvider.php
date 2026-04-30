<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contract for external system integration adapters.
 *
 * This establishes a clean abstraction boundary so that when
 * connecting to external systems (Noor, Fares, etc.) in the future,
 * the implementation can be swapped without changing application code.
 *
 * Phase 1: NullExternalAdapter (no-op, returns empty data)
 * Phase 2: NoorAdapter, FaresAdapter, etc.
 */
interface ExternalStudentProvider
{
    /**
     * Look up a student's basic info by national ID.
     *
     * @return array{
     *   found: bool,
     *   national_id?: string,
     *   full_name?: string,
     *   gender?: string,
     *   birth_date?: string,
     *   school_name?: string,
     *   grade_level?: int,
     *   source: string,
     * }
     */
    public function findByNationalId(string $nationalId): array;

    /**
     * Sync student enrollment status from the external system.
     *
     * @return array{synced: bool, message: string}
     */
    public function syncEnrollmentStatus(string $nationalId): array;

    /**
     * Check connectivity to the external system.
     */
    public function healthCheck(): bool;

    /**
     * Get the provider name (e.g., "noor", "fares", "null").
     */
    public function providerName(): string;
}
