<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class StudentController
{
    public function index(): void
    {
        // List students in current access scope.
    }

    public function store(): void
    {
        // Create student profile.
    }

    public function show(): void
    {
        // Return student profile and linked entities.
    }

    public function update(): void
    {
        // Update student profile.
    }

    public function archive(): void
    {
        // Archive student instead of deleting.
    }

    public function guardians(): void
    {
        // List guardians for the given student.
    }

    public function assignGuardian(): void
    {
        // Attach guardian to student.
    }
}
