<?php

declare(strict_types=1);

namespace App\Services;

final class NotificationService
{
    public function listForCurrentUser(): void
    {
        // Return notification feed.
    }

    public function send(): void
    {
        // Dispatch notification through configured channels.
    }

    public function markRead(): void
    {
        // Mark notification as read.
    }

    public function markAllRead(): void
    {
        // Mark all notifications as read.
    }
}
