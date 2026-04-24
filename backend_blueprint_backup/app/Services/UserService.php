<?php

declare(strict_types=1);

namespace App\Services;

final class UserService
{
    public function paginate(): void
    {
        // Return filtered users.
    }

    public function create(): void
    {
        // Persist new user and default assignments.
    }

    public function update(): void
    {
        // Update user profile and role constraints.
    }

    public function changeStatus(): void
    {
        // Activate, deactivate, or lock user.
    }

    public function assignSchools(): void
    {
        // Sync user-school assignments.
    }
}
