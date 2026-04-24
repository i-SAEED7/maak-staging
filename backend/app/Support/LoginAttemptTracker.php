<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\AuthPortalException;
use App\Models\LoginAttempt;

final class LoginAttemptTracker
{
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCK_MINUTES = 10;
    private const WINDOW_MINUTES = 15;

    public function ensureNotLocked(string $identifier, ?string $schoolCode, ?string $ipAddress): void
    {
        $activeLock = $this->attemptQuery($identifier, $schoolCode, $ipAddress)
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->latest('attempted_at')
            ->first();

        if ($activeLock !== null) {
            throw new AuthPortalException('تم إيقاف المحاولات مؤقتًا. حاول مرة أخرى لاحقًا.', 429);
        }
    }

    public function recordSuccess(string $identifier, ?string $schoolCode, ?string $ipAddress, ?string $userAgent): void
    {
        LoginAttempt::query()->create([
            'identifier' => $identifier,
            'school_code' => $schoolCode,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'success' => true,
            'attempted_at' => now(),
            'locked_until' => null,
        ]);
    }

    public function recordFailure(string $identifier, ?string $schoolCode, ?string $ipAddress, ?string $userAgent): void
    {
        $failedAttempts = $this->attemptQuery($identifier, $schoolCode, $ipAddress)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes(self::WINDOW_MINUTES))
            ->count();

        $lockedUntil = $failedAttempts + 1 >= self::MAX_FAILED_ATTEMPTS
            ? now()->addMinutes(self::LOCK_MINUTES)
            : null;

        LoginAttempt::query()->create([
            'identifier' => $identifier,
            'school_code' => $schoolCode,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'success' => false,
            'attempted_at' => now(),
            'locked_until' => $lockedUntil,
        ]);
    }

    private function attemptQuery(string $identifier, ?string $schoolCode, ?string $ipAddress)
    {
        return LoginAttempt::query()
            ->where('identifier', $identifier)
            ->when(
                $schoolCode !== null,
                fn ($query) => $query->where('school_code', $schoolCode),
                fn ($query) => $query->whereNull('school_code'),
            )
            ->when($ipAddress !== null, fn ($query) => $query->where('ip_address', $ipAddress));
    }
}
