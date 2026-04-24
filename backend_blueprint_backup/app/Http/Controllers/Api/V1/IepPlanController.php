<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class IepPlanController
{
    public function index(): void
    {
        // List IEP plans with status filters.
    }

    public function store(): void
    {
        // Create draft IEP plan.
    }

    public function show(): void
    {
        // Return plan details with goals and comments.
    }

    public function update(): void
    {
        // Update draft plan.
    }

    public function submit(): void
    {
        // Transition plan to principal review.
    }

    public function principalApprove(): void
    {
        // Transition plan to supervisor review.
    }

    public function supervisorApprove(): void
    {
        // Approve plan and dispatch PDF generation.
    }

    public function reject(): void
    {
        // Reject plan with reason.
    }

    public function versions(): void
    {
        // Return version history.
    }

    public function comment(): void
    {
        // Add workflow comment.
    }

    public function pdf(): void
    {
        // Return generated PDF or temporary link.
    }
}
