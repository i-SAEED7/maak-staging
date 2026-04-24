<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SupervisorVisit;
use App\Models\User;

final class SupervisorVisitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('supervision.view_any');
    }

    public function view(User $user, SupervisorVisit $visit): bool
    {
        return $user->hasPermission('supervision.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('supervision.create');
    }

    public function update(User $user, SupervisorVisit $visit): bool
    {
        return $user->hasPermission('supervision.update');
    }

    public function complete(User $user, SupervisorVisit $visit): bool
    {
        return $user->hasPermission('supervision.complete');
    }

    public function addRecommendation(User $user, SupervisorVisit $visit): bool
    {
        return $user->hasPermission('supervision.add_recommendation');
    }

    public function updateRecommendation(User $user): bool
    {
        return $user->hasPermission('supervision.update_recommendation');
    }
}
