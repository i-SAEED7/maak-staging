<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class AuditLogService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginate(User $actor, array $filters = []): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $perPage = max(10, min((int) ($filters['per_page'] ?? 20), 100));

        return AuditLog::query()
            ->with(['actor.role', 'school'])
            ->when($actor->role?->name !== 'super_admin', function ($query) use ($actor): void {
                $schoolIds = $this->tenantService->accessibleSchoolIds($actor);

                $query->where(function ($builder) use ($actor, $schoolIds): void {
                    $builder->where('user_id', $actor->id);

                    if ($schoolIds !== []) {
                        $builder->orWhereIn('school_id', $schoolIds);
                    }
                });
            })
            ->when($filters['action'] ?? null, fn ($query, $value) => $query->where('action', $value))
            ->when($filters['user_id'] ?? null, fn ($query, $value) => $query->where('user_id', (int) $value))
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', (int) $value))
            ->when($filters['target_type'] ?? null, fn ($query, $value) => $query->where('target_type', 'like', '%' . $value . '%'))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('action', 'like', '%' . $search . '%')
                        ->orWhere('target_type', 'like', '%' . $search . '%')
                        ->orWhere('endpoint', 'like', '%' . $search . '%')
                        ->orWhereHas('actor', fn ($actorQuery) => $actorQuery->where('full_name', 'like', '%' . $search . '%'))
                        ->orWhereHas('school', fn ($schoolQuery) => $schoolQuery->where('name_ar', 'like', '%' . $search . '%'));
                });
            })
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
