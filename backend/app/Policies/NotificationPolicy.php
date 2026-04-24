<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

final class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('notifications.view_any');
    }

    public function markRead(User $user, ?Notification $notification = null): bool
    {
        return $user->hasPermission('notifications.mark_read');
    }
}
