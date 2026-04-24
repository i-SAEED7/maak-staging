<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class NotificationController
{
    public function index(): void
    {
        // List notifications for current user.
    }

    public function markRead(): void
    {
        // Mark a single notification as read.
    }

    public function markAllRead(): void
    {
        // Mark all current user notifications as read.
    }
}
