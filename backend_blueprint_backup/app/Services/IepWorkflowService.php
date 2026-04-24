<?php

declare(strict_types=1);

namespace App\Services;

final class IepWorkflowService
{
    public function submit(): void
    {
        // Move draft to principal review.
    }

    public function principalApprove(): void
    {
        // Move plan to supervisor review.
    }

    public function supervisorApprove(): void
    {
        // Approve plan and dispatch downstream jobs.
    }

    public function reject(): void
    {
        // Reject plan and record reason.
    }
}
