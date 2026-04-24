<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\School;
use App\Models\User;

final class SchoolPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('schools.view_any');
    }

    public function view(User $user, School $school): bool
    {
        return $user->hasPermission('schools.view')
            || $this->isLinkedToSchool($user, $school);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user)
            && $user->hasPermission('schools.create');
    }

    public function update(User $user, School $school): bool
    {
        return $this->isSuperAdmin($user)
            && $user->hasPermission('schools.update');
    }

    public function changeStatus(User $user, School $school): bool
    {
        return $this->isSuperAdmin($user)
            && $user->hasPermission('schools.change_status');
    }

    public function delete(User $user, School $school): bool
    {
        return $this->changeStatus($user, $school);
    }

    public function assignPrincipal(User $user, School $school): bool
    {
        return $this->isSuperAdmin($user)
            && $user->hasPermission('schools.assign_principal');
    }

    public function assignSupervisor(User $user, School $school): bool
    {
        return $this->isSuperAdmin($user)
            && $user->hasPermission('schools.assign_supervisor');
    }

    public function stats(User $user, School $school): bool
    {
        return $this->view($user, $school);
    }

    private function isSuperAdmin(User $user): bool
    {
        return $user->role?->name === RoleName::SUPER_ADMIN;
    }

    private function isLinkedToSchool(User $user, School $school): bool
    {
        if ($this->isSuperAdmin($user) || $user->role?->name === RoleName::ADMIN) {
            return true;
        }

        if ((int) $user->school_id === (int) $school->id) {
            return true;
        }

        return $user->assignedSchools()
            ->whereKey($school->id)
            ->exists();
    }
}
