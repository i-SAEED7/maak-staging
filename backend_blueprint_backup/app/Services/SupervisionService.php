<?php

declare(strict_types=1);

namespace App\Services;

final class SupervisionService
{
    public function paginateVisits(): void
    {
        // Return filtered visits for current supervisor scope.
    }

    public function createVisit(): void
    {
        // Schedule supervision visit.
    }

    public function updateVisit(): void
    {
        // Update visit details.
    }

    public function completeVisit(): void
    {
        // Finalize visit and compute score.
    }

    public function addRecommendation(): void
    {
        // Persist recommendation entry.
    }

    public function updateRecommendation(): void
    {
        // Update recommendation workflow state.
    }
}
