<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

final class MessageController
{
    public function index(): void
    {
        // List message threads for current user.
    }

    public function thread(): void
    {
        // Return messages in selected thread.
    }

    public function store(): void
    {
        // Send a new message.
    }

    public function markRead(): void
    {
        // Mark message as read for current recipient.
    }
}
