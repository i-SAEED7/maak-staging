<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = max(5, min((int) ($filters['per_page'] ?? 15), 100));
        $search = trim((string) ($filters['search'] ?? ''));
        $actor = auth()->user();
        $accessibleSchoolIds = $actor ? $this->tenantService->accessibleSchoolIds($actor) : [];
        $isSuperAdmin = $actor?->role?->name === 'super_admin';

        return User::query()
            ->with(['role', 'school', 'assignedSchools'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('school', fn (Builder $schoolQuery) => $schoolQuery->where('name_ar', 'like', "%{$search}%"));
                });
            })
            ->when($filters['role'] ?? null, function ($query, $value) {
                $query->whereHas('role', fn ($roleQuery) => $roleQuery->where('name', $value));
            })
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when(! $isSuperAdmin && $accessibleSchoolIds !== [], function ($query) use ($accessibleSchoolIds) {
                $query->where(function (Builder $userQuery) use ($accessibleSchoolIds): void {
                    $userQuery
                        ->whereIn('school_id', $accessibleSchoolIds)
                        ->orWhereHas('schoolAssignments', fn (Builder $assignmentQuery) => $assignmentQuery->whereIn('school_id', $accessibleSchoolIds));
                });
            })
            ->orderBy('full_name')
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        $role = Role::query()->where('name', $data['role'])->first();

        if (! $role) {
            throw new InvalidArgumentException('الدور المطلوب غير موجود.');
        }

        if (! empty($data['school_id']) && ! School::query()->whereKey($data['school_id'])->exists()) {
            throw new InvalidArgumentException('المدرسة المحددة غير موجودة.');
        }

        return DB::transaction(function () use ($data, $role): User {
            $primarySchoolId = $this->resolvePrimarySchoolId($data, $role->name);
            $user = User::query()->create([
                'uuid' => (string) Str::uuid(),
                'role_id' => $role->id,
                'school_id' => $primarySchoolId,
                'full_name' => $data['full_name'],
                'username' => $this->generateUsername($data),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'national_id_encrypted' => $data['national_id'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'status' => 'active',
                'is_central' => $this->determineCentralFlag($role->name),
                'must_change_password' => (bool) ($data['must_change_password'] ?? false),
                'locale' => 'ar',
            ]);

            $this->syncAssignedSchoolsForRole($user, $role->name, $data);

            return $user->refresh()->load(['role', 'school', 'assignedSchools']);
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $roleName = $user->role?->name ?? 'teacher';
            $roleId = $user->role_id;

            if (! empty($data['role'])) {
                $role = Role::query()->where('name', $data['role'])->first();
                if ($role) {
                    $roleName = $role->name;
                    $roleId = $role->id;
                }
            }

            $payload = [
                'school_id' => array_key_exists('school_id', $data)
                    ? $this->resolvePrimarySchoolId($data, $roleName, $user->school_id)
                    : $this->resolvePrimarySchoolId(['school_ids' => $data['school_ids'] ?? null], $roleName, $user->school_id),
                'full_name' => $data['full_name'] ?? $user->full_name,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                'national_id_encrypted' => $data['national_id'] ?? $user->national_id_encrypted,
                'must_change_password' => $data['must_change_password'] ?? $user->must_change_password,
                'role_id' => $roleId,
                'is_central' => $this->determineCentralFlag($roleName),
            ];

            if (! empty($data['email']) || ! empty($data['phone'])) {
                $payload['username'] = $this->generateUsername([
                    'email' => $data['email'] ?? $user->email,
                    'phone' => $data['phone'] ?? $user->phone,
                ], $user->id, $user->username);
            }

            if (! empty($data['password'])) {
                $payload['password_hash'] = Hash::make($data['password']);
            }

            $user->update($payload);
            $this->syncAssignedSchoolsForRole($user, $roleName, $data);

            return $user->refresh()->load(['role', 'school', 'assignedSchools']);
        });
    }

    public function changeStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);

        return $user->refresh()->load(['role', 'school', 'assignedSchools']);
    }

    public function deactivate(User $user): User
    {
        $user->update(['status' => 'inactive']);

        return $user->refresh()->load(['role', 'school', 'assignedSchools']);
    }

    public function assignSchools(User $user, array $schoolIds, string $assignmentType = 'supporting'): void
    {
        DB::table('user_school_assignments')->where('user_id', $user->id)->delete();

        foreach ($schoolIds as $schoolId) {
            DB::table('user_school_assignments')->insert([
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'assignment_type' => $assignmentType,
                'created_at' => now(),
            ]);
        }
    }

    private function syncAssignedSchoolsForRole(User $user, string $roleName, array $data): void
    {
        $hasSchoolIds = array_key_exists('school_ids', $data);
        $schoolIds = $this->normalizeSchoolIds($data['school_ids'] ?? []);

        if ($roleName === 'supervisor') {
            if (! $hasSchoolIds && $user->role?->name === 'supervisor') {
                return;
            }

            if ($schoolIds === [] && $user->school_id !== null) {
                $schoolIds = [$user->school_id];
            }

            $this->assignSchools($user, $schoolIds, 'supervising');

            return;
        }

        if ($this->determineCentralFlag($roleName)) {
            $this->assignSchools($user, []);

            return;
        }

        $primarySchoolId = isset($data['school_id']) && $data['school_id'] !== ''
            ? (int) $data['school_id']
            : $user->school_id;

        $this->assignSchools($user, $primarySchoolId ? [$primarySchoolId] : [], 'member');
    }

    private function resolvePrimarySchoolId(array $data, string $roleName, ?int $fallbackSchoolId = null): ?int
    {
        $schoolIds = $this->normalizeSchoolIds($data['school_ids'] ?? []);
        $requestedSchoolId = isset($data['school_id']) && $data['school_id'] !== ''
            ? (int) $data['school_id']
            : null;

        if ($roleName === 'supervisor') {
            if ($requestedSchoolId !== null) {
                return $requestedSchoolId;
            }

            if ($schoolIds !== []) {
                return $schoolIds[0];
            }
        }

        return $requestedSchoolId ?? $fallbackSchoolId;
    }

    /**
     * @param  array<int, mixed>|mixed  $schoolIds
     * @return array<int, int>
     */
    private function normalizeSchoolIds(mixed $schoolIds): array
    {
        if (! is_array($schoolIds)) {
            return [];
        }

        return collect($schoolIds)
            ->map(static fn (mixed $schoolId): int => (int) $schoolId)
            ->filter(static fn (int $schoolId): bool => $schoolId > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function determineCentralFlag(string $roleName): bool
    {
        return in_array($roleName, ['super_admin', 'admin', 'supervisor'], true);
    }

    private function generateUsername(array $data, ?int $ignoreUserId = null, ?string $fallback = null): string
    {
        $base = null;

        if (! empty($data['email'])) {
            $base = Str::of((string) $data['email'])->before('@')->lower()->replaceMatches('/[^a-z0-9._-]/', '');
        } elseif (! empty($data['phone'])) {
            $base = Str::of((string) $data['phone'])->replaceMatches('/[^0-9]/', '');
        } elseif ($fallback) {
            $base = $fallback;
        }

        $base = trim((string) $base);

        if ($base === '') {
            $base = 'user';
        }

        $candidate = Str::limit($base, 60, '');
        $suffix = 1;

        while (
            User::query()
                ->where('username', $candidate)
                ->when($ignoreUserId !== null, fn (Builder $query) => $query->where('id', '!=', $ignoreUserId))
                ->exists()
        ) {
            $candidate = Str::limit($base, max(1, 60 - strlen((string) $suffix) - 1), '') . '_' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}
