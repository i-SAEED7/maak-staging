<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\AuditLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class AuditLogger
{
    public function log(
        ?User $actor,
        string $action,
        Model|string|null $target = null,
        array $oldValues = [],
        array $newValues = [],
        ?int $schoolId = null,
    ): void
    {
        $request = request();

        AuditLog::query()->create([
            'school_id' => $schoolId ?? $this->resolveSchoolId($target),
            'user_id' => $actor?->id,
            'action' => $action,
            'target_type' => $this->resolveTargetType($target),
            'target_id' => $target instanceof Model ? $target->getKey() : null,
            'method' => $request?->method(),
            'endpoint' => $request?->path(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'old_values' => $oldValues === [] ? null : $oldValues,
            'new_values' => $newValues === [] ? null : $newValues,
            'created_at' => now(),
        ]);
    }

    private function resolveTargetType(Model|string|null $target): ?string
    {
        if (is_string($target)) {
            return $target;
        }

        if ($target instanceof Model) {
            return class_basename($target);
        }

        return null;
    }

    private function resolveSchoolId(Model|string|null $target): ?int
    {
        if (! $target instanceof Model) {
            return null;
        }

        if ($target instanceof School) {
            return (int) $target->getKey();
        }

        if (isset($target->school_id) && $target->school_id !== null) {
            return (int) $target->school_id;
        }

        return null;
    }
}
