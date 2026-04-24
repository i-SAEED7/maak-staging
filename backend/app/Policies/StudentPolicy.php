<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

final class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students.view_any')
            || ($user->role?->name === 'parent' && $user->hasPermission('students.view'));
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasPermission('students.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('students.create');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermission('students.update');
    }

    public function archive(User $user, Student $student): bool
    {
        return $user->hasPermission('students.archive');
    }

    public function assignGuardian(User $user, Student $student): bool
    {
        return $user->hasPermission('students.assign_guardian');
    }
}
