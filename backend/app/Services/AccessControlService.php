<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AccessControlService
{
    public function roles(): array
    {
        return Role::query()
            ->with(['permissions' => fn ($query) => $query->orderBy('module')->orderBy('display_name_ar')])
            ->orderBy('id')
            ->get()
            ->map(static fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name_ar' => $role->display_name_ar,
                'description' => $role->description,
                'permissions' => $role->permissions
                    ->map(static fn (Permission $permission): array => [
                        'id' => $permission->id,
                        'key' => $permission->key,
                        'display_name_ar' => $permission->display_name_ar,
                        'module' => $permission->module,
                    ])
                    ->values()
                    ->all(),
            ])
            ->all();
    }

    public function permissions(): array
    {
        return Permission::query()
            ->orderBy('module')
            ->orderBy('display_name_ar')
            ->get()
            ->groupBy('module')
            ->map(static fn ($permissions, string $module): array => [
                'module' => $module,
                'permissions' => collect($permissions)
                    ->map(static fn (Permission $permission): array => [
                        'id' => $permission->id,
                        'key' => $permission->key,
                        'display_name_ar' => $permission->display_name_ar,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    public function updateRolePermissions(Role $role, array $permissionKeys): Role
    {
        $permissionIds = Permission::query()
            ->whereIn('key', $permissionKeys)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($permissionIds);

        return $role->load(['permissions' => fn ($query) => $query->orderBy('module')->orderBy('display_name_ar')]);
    }

    public function usersForRole(Role $role): array
    {
        return User::query()
            ->with(['role.permissions', 'school', 'assignedSchools', 'directPermissions'])
            ->where('role_id', $role->id)
            ->orderBy('full_name')
            ->get()
            ->map(static fn (User $user): array => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'status' => $user->status,
                'school' => $user->school ? [
                    'id' => $user->school->id,
                    'name_ar' => $user->school->name_ar,
                ] : null,
                'assigned_schools' => $user->assignedSchools
                    ->map(static fn ($school): array => [
                        'id' => $school->id,
                        'name_ar' => $school->name_ar,
                    ])
                    ->values()
                    ->all(),
                'effective_permission_keys' => $user->permissionKeys(),
                'direct_permission_overrides' => [
                    'allow' => $user->directPermissions
                        ->filter(static fn (Permission $permission): bool => $permission->pivot?->effect === 'allow')
                        ->pluck('key')
                        ->values()
                        ->all(),
                    'deny' => $user->directPermissions
                        ->filter(static fn (Permission $permission): bool => $permission->pivot?->effect === 'deny')
                        ->pluck('key')
                        ->values()
                        ->all(),
                ],
            ])
            ->all();
    }

    public function updateUserPermissions(Role $role, array $permissionKeys, array $userIds = [], bool $applyToAll = false): array
    {
        $targetUsers = User::query()
            ->with(['role.permissions', 'directPermissions'])
            ->where('role_id', $role->id)
            ->when(! $applyToAll, fn ($query) => $query->whereIn('id', $userIds))
            ->get();

        if ($targetUsers->isEmpty()) {
            throw ValidationException::withMessages([
                'user_ids' => ['يجب تحديد حساب واحد على الأقل داخل هذا الدور.'],
            ]);
        }

        $rolePermissionKeys = $role->permissions()
            ->pluck('key')
            ->values()
            ->all();

        $requestedPermissionKeys = collect($permissionKeys)
            ->map(static fn (mixed $permissionKey): string => (string) $permissionKey)
            ->unique()
            ->values()
            ->all();

        $allowKeys = array_values(array_diff($requestedPermissionKeys, $rolePermissionKeys));
        $denyKeys = array_values(array_diff($rolePermissionKeys, $requestedPermissionKeys));
        $overridePermissionModels = Permission::query()
            ->whereIn('key', [...$allowKeys, ...$denyKeys])
            ->get()
            ->keyBy('key');

        DB::transaction(function () use ($targetUsers, $allowKeys, $denyKeys, $overridePermissionModels): void {
            foreach ($targetUsers as $user) {
                UserPermission::query()->where('user_id', $user->id)->delete();

                foreach ($allowKeys as $allowKey) {
                    $permission = $overridePermissionModels->get($allowKey);

                    if ($permission === null) {
                        continue;
                    }

                    UserPermission::query()->create([
                        'user_id' => $user->id,
                        'permission_id' => $permission->id,
                        'effect' => 'allow',
                    ]);
                }

                foreach ($denyKeys as $denyKey) {
                    $permission = $overridePermissionModels->get($denyKey);

                    if ($permission === null) {
                        continue;
                    }

                    UserPermission::query()->create([
                        'user_id' => $user->id,
                        'permission_id' => $permission->id,
                        'effect' => 'deny',
                    ]);
                }
            }
        });

        return $this->usersForRole($role);
    }
}
