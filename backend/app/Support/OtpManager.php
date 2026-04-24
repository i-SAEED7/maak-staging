<?php

declare(strict_types=1);

namespace App\Support;

final class OtpManager
{
    public function issue(): void
    {
        // Generate and persist OTP for reset flow.
    }

    public function verify(): bool
    {
        // Verify OTP and expiration rules.
        return false;
    }
}
