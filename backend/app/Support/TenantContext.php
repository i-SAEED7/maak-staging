<?php

declare(strict_types=1);

namespace App\Support;

final class TenantContext
{
    private ?int $schoolId = null;

    public function setSchoolId(?int $schoolId): void
    {
        $this->schoolId = $schoolId;
    }

    public function schoolId(): ?int
    {
        return $this->schoolId;
    }

    public function hasSchool(): bool
    {
        return $this->schoolId !== null;
    }

    public function clear(): void
    {
        $this->schoolId = null;
    }
}
