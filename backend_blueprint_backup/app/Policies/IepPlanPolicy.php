<?php

declare(strict_types=1);

namespace App\Policies;

final class IepPlanPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function submit(): bool
    {
        return true;
    }

    public function approve(): bool
    {
        return true;
    }

    public function reject(): bool
    {
        return true;
    }
}
