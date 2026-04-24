<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TenantService
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function resolveSchoolIdForRequest(User $user, ?int $requestedSchoolId = null, bool $required = false): ?int
    {
        if ($this->isBypassRole($user)) {
            if ($requestedSchoolId !== null) {
                $this->assertSchoolExists($requestedSchoolId);
            }

            $this->tenantContext->setSchoolId($requestedSchoolId);

            return $requestedSchoolId;
        }

        $accessibleSchoolIds = $this->accessibleSchoolIds($user);
        $requiresMultipleSchoolScope = $this->requiresMultipleSchoolScope($user, $accessibleSchoolIds);

        if ($requestedSchoolId !== null) {
            if (! in_array($requestedSchoolId, $accessibleSchoolIds, true)) {
                throw new AuthorizationException('لا تملك صلاحية الوصول إلى هذه المدرسة.');
            }

            $this->tenantContext->setSchoolId($requestedSchoolId);

            return $requestedSchoolId;
        }

        if (! $requiresMultipleSchoolScope && $user->school_id !== null) {
            $this->tenantContext->setSchoolId((int) $user->school_id);

            return (int) $user->school_id;
        }

        if (count($accessibleSchoolIds) === 1) {
            $this->tenantContext->setSchoolId($accessibleSchoolIds[0]);

            return $accessibleSchoolIds[0];
        }

        if ($required) {
            if (count($accessibleSchoolIds) > 1 && ! $requiresMultipleSchoolScope) {
                throw ValidationException::withMessages([
                    'school_id' => ['يجب تحديد المدرسة الحالية لأن المستخدم مرتبط بأكثر من مدرسة.'],
                ]);
            }

            if (count($accessibleSchoolIds) === 0) {
                throw ValidationException::withMessages([
                    'school_id' => ['تعذر تحديد المدرسة الحالية لهذا المستخدم.'],
                ]);
            }
        }

        $this->tenantContext->clear();

        return null;
    }

    public function accessibleSchoolIds(User $user): array
    {
        if ($this->isBypassRole($user)) {
            return School::query()->pluck('id')->map(static fn (mixed $id): int => (int) $id)->all();
        }

        $schoolIds = [];

        if ($user->school_id !== null) {
            $schoolIds[] = (int) $user->school_id;
        }

        $assignedSchoolIds = DB::table(config('tenancy.supervisor_assignment_table', 'user_school_assignments'))
            ->where('user_id', $user->id)
            ->pluck('school_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        return array_values(array_unique([...$schoolIds, ...$assignedSchoolIds]));
    }

    public function assertCanAccessSchoolId(User $user, ?int $schoolId): void
    {
        if ($schoolId === null || $this->isBypassRole($user)) {
            return;
        }

        if (! in_array($schoolId, $this->accessibleSchoolIds($user), true)) {
            throw new AuthorizationException('لا تملك صلاحية الوصول إلى هذا السجل.');
        }
    }

    public function scopeAccessibleSchools(Builder $query, User $user, string $column = 'school_id'): Builder
    {
        if ($this->isBypassRole($user) || $user->role?->name === 'admin') {
            return $query;
        }

        $accessibleSchoolIds = $this->accessibleSchoolIds($user);

        if ($accessibleSchoolIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($column, $accessibleSchoolIds);
    }

    private function assertSchoolExists(int $schoolId): void
    {
        if (! School::query()->whereKey($schoolId)->exists()) {
            throw ValidationException::withMessages([
                'school_id' => ['المدرسة المحددة غير موجودة.'],
            ]);
        }
    }

    private function isBypassRole(User $user): bool
    {
        $roleName = $user->role?->name;

        return $roleName !== null
            && in_array($roleName, config('tenancy.bypass_roles', []), true);
    }

    private function requiresMultipleSchoolScope(User $user, array $accessibleSchoolIds): bool
    {
        return $user->role?->name === 'supervisor'
            && count($accessibleSchoolIds) > 1;
    }
}
