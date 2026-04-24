<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

final class PermissionResolver
{
    public function resolveForUser(User $user): array
    {
        $user->loadMissing('role.permissions', 'directPermissions');

        if ($user->role?->name === 'super_admin') {
            return ['*'];
        }

        return $user->permissionKeys();
    }
}
