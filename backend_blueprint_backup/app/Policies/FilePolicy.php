<?php

declare(strict_types=1);

namespace App\Policies;

final class FilePolicy
{
    public function upload(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function download(): bool
    {
        return true;
    }

    public function delete(): bool
    {
        return true;
    }
}
