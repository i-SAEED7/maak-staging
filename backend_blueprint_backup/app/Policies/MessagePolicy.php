<?php

declare(strict_types=1);

namespace App\Policies;

final class MessagePolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function viewThread(): bool
    {
        return true;
    }

    public function send(): bool
    {
        return true;
    }
}
