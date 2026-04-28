<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
            ->where('created_at', '>=', now()->subMonths(3))
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

    public function archiveOlderThanThreeMonths(int $chunkSize = 500): int
    {
        $archivedCount = 0;
        $cutoff = now()->subMonths(3);

        AuditLog::query()
            ->where('created_at', '<', $cutoff)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($logs) use (&$archivedCount): void {
                DB::transaction(function () use ($logs, &$archivedCount): void {
                    $rows = $logs->map(static fn (AuditLog $log): array => [
                        'original_audit_log_id' => $log->id,
                        'school_id' => $log->school_id,
                        'user_id' => $log->user_id,
                        'action' => $log->action,
                        'target_type' => $log->target_type,
                        'target_id' => $log->target_id,
                        'method' => $log->method,
                        'endpoint' => $log->endpoint,
                        'ip_address' => $log->ip_address,
                        'user_agent' => $log->user_agent,
                        'old_values' => $log->old_values !== null ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : null,
                        'new_values' => $log->new_values !== null ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : null,
                        'created_at' => $log->created_at,
                        'archived_at' => now(),
                    ])->all();

                    if ($rows !== []) {
                        DB::table('audit_log_archives')->insert($rows);
                        AuditLog::query()->whereIn('id', $logs->pluck('id')->all())->delete();
                        $archivedCount += count($rows);
                    }
                });
            });

        return $archivedCount;
    }
}
