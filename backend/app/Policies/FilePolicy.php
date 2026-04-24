<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\File;
use App\Models\User;

final class FilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('files.view');
    }

    public function upload(User $user): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('files.upload');
    }

    public function view(User $user, File $file): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('files.view');
    }

    public function download(User $user, File $file): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('files.download');
    }

    public function delete(User $user, File $file): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('files.delete');
    }
}
