<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

final class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('announcements.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('announcements.create');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('announcements.view_any');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if (! $user->hasPermission('announcements.update')) {
            return false;
        }

        if ($user->role?->name === 'super_admin') {
            return true;
        }

        return $user->role?->name === 'principal'
            && $announcement->school_id !== null
            && (int) $announcement->school_id === (int) $user->school_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement)
            && $user->hasPermission('announcements.delete');
    }
}
