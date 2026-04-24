<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleName;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DashboardService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function summary(User $actor): array
    {
        return $this->summaryWithFilters($actor, []);
    }

    public function summaryWithFilters(User $actor, array $filters): array
    {
        $schoolIds = $this->resolveSchoolIds($actor, $filters);

        return [
            'schools_count' => $this->schoolsCount($actor, $schoolIds),
            'students_count' => $this->studentsCount($actor, $schoolIds),
            'programs_count' => $this->programsCount($actor, $schoolIds),
            'teachers_count' => $this->usersCountByRole($actor, $schoolIds, RoleName::TEACHER),
            'supervisors_count' => $this->usersCountByRole($actor, $schoolIds, RoleName::SUPERVISOR),
            'principals_count' => $this->usersCountByRole($actor, $schoolIds, RoleName::PRINCIPAL),
            'context_school_name' => $this->contextSchoolName($actor, $schoolIds),
            'context_school_code' => $this->contextSchoolCode($actor, $schoolIds),
            'context_principal_name' => $this->contextPrincipalName($actor, $schoolIds),
            'map_placeholder' => [
                'enabled' => false,
                'message' => 'تم تجهيز مساحة الخريطة لاحقًا لعرض المدارس على الخارطة.',
            ],
            'active_filters' => [
                'school_id' => $filters['school_id'] ?? null,
                'program_type' => $filters['program_type'] ?? null,
            ],
        ];
    }

    private function schoolsCount(User $actor, array $schoolIds): int
    {
        return (int) School::query()->whereIn('id', $schoolIds)->count();
    }

    private function studentsCount(User $actor, array $schoolIds): int
    {
        $query = DB::table('students');

        if ($actor->role?->name !== 'super_admin') {
            $query->whereIn('school_id', $schoolIds);
        }

        return (int) $query->count();
    }

    private function programsCount(User $actor, array $schoolIds): int
    {
        $query = School::query()->whereNotNull('program_type');

        if ($actor->role?->name !== 'super_admin') {
            $query->whereIn('id', $schoolIds);
        }

        return (int) $query->distinct('program_type')->count('program_type');
    }

    private function usersCountByRole(User $actor, array $schoolIds, string $roleName): int
    {
        if (in_array($roleName, [RoleName::SUPERVISOR, RoleName::PRINCIPAL], true)) {
            $column = $roleName === RoleName::SUPERVISOR ? 'supervisor_id' : 'principal_id';
            $ids = School::query()
                ->whereIn('id', $schoolIds)
                ->whereNotNull($column)
                ->pluck($column)
                ->map(static fn (mixed $id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            if ($ids === []) {
                return 0;
            }

            return (int) DB::table('users')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.name', $roleName)
                ->where('users.status', 'active')
                ->whereIn('users.id', $ids)
                ->count('users.id');
        }

        $query = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('roles.name', $roleName);

        if ($actor->role?->name !== 'super_admin') {
            $query->where(function ($builder) use ($schoolIds): void {
                $builder->whereIn('users.school_id', $schoolIds)
                    ->orWhereExists(function ($subQuery) use ($schoolIds): void {
                        $subQuery
                            ->selectRaw('1')
                            ->from('user_school_assignments')
                            ->whereColumn('user_school_assignments.user_id', 'users.id')
                            ->whereIn('user_school_assignments.school_id', $schoolIds);
                    });
            });
        }

        $query->where('users.status', 'active');

        return (int) $query->distinct('users.id')->count('users.id');
    }

    private function resolveSchoolIds(User $actor, array $filters): array
    {
        $allowedSchoolIds = $this->tenantService->accessibleSchoolIds($actor);

        if ($allowedSchoolIds === []) {
            return [];
        }

        $requestedSchoolId = isset($filters['school_id']) && $filters['school_id'] !== ''
            ? (int) $filters['school_id']
            : null;
        $programType = isset($filters['program_type']) && $filters['program_type'] !== ''
            ? (string) $filters['program_type']
            : null;

        if ($requestedSchoolId !== null) {
            $this->tenantService->assertCanAccessSchoolId($actor, $requestedSchoolId);
            $allowedSchoolIds = array_values(array_filter(
                $allowedSchoolIds,
                static fn (int $schoolId): bool => $schoolId === $requestedSchoolId,
            ));
        }

        if ($programType !== null) {
            $allowedSchoolIds = School::query()
                ->whereIn('id', $allowedSchoolIds)
                ->where('program_type', $programType)
                ->pluck('id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->all();
        }

        return $allowedSchoolIds;
    }

    private function contextSchoolName(User $actor, array $schoolIds): ?string
    {
        if ($actor->role?->name === 'super_admin' || $actor->role?->name === 'admin') {
            return null;
        }

        if (count($schoolIds) !== 1) {
            return 'مدارس متعددة';
        }

        return School::query()->whereKey($schoolIds[0])->value('name_ar');
    }

    private function contextSchoolCode(User $actor, array $schoolIds): ?string
    {
        if ($actor->role?->name === 'super_admin' || $actor->role?->name === 'admin') {
            return null;
        }

        if (count($schoolIds) !== 1) {
            return null;
        }

        return School::query()->whereKey($schoolIds[0])->value('school_code');
    }

    private function contextPrincipalName(User $actor, array $schoolIds): ?string
    {
        if ($actor->role?->name === 'super_admin' || $actor->role?->name === 'admin') {
            return null;
        }

        if (count($schoolIds) !== 1) {
            return null;
        }

        return School::query()
            ->whereKey($schoolIds[0])
            ->with('principal')
            ->first()
            ?->principal
            ?->full_name;
    }
}
