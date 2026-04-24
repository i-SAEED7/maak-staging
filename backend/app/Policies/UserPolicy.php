<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, User $targetUser): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, User $targetUser): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function changeStatus(User $user, User $targetUser): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function assignSchools(User $user, User $targetUser): bool
    {
        return $this->isSuperAdmin($user);
    }

    private function isSuperAdmin(User $user): bool
    {
        return $user->role?->name === 'super_admin';
    }
}
