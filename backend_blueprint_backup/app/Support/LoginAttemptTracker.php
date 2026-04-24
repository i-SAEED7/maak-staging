<?php

declare(strict_types=1);

namespace App\Support;

final class LoginAttemptTracker
{
    public function recordSuccess(): void
    {
        // Store successful login attempt.
    }

    public function recordFailure(): void
    {
        // Store failed login attempt and compute lock window.
    }
}
