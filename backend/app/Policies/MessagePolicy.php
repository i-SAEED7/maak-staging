<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

final class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('messages.view_any');
    }

    public function viewThread(User $user): bool
    {
        return $user->hasPermission('messages.view_thread');
    }

    public function send(User $user): bool
    {
        return $user->hasPermission('messages.send');
    }

    public function markRead(User $user, ?Message $message = null): bool
    {
        return $user->hasPermission('messages.mark_read');
    }
}
