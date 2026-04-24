<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\IepPlan;
use App\Models\User;

final class IepPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('iep.view_any')
            || ($user->role?->name === 'parent' && $user->hasPermission('iep.view'));
    }

    public function view(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('iep.create');
    }

    public function update(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.update');
    }

    public function submit(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.submit');
    }

    public function principalApprove(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.principal_approve');
    }

    public function supervisorApprove(User $user, IepPlan $iepPlan): bool
    {
        return false;
    }

    public function acknowledge(User $user, IepPlan $iepPlan): bool
    {
        return $user->role?->name === 'parent' && $user->hasPermission('iep.view');
    }

    public function reject(User $user, IepPlan $iepPlan): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('iep.reject');
    }

    public function comment(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.comment');
    }

    public function versions(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.view_versions');
    }

    public function pdf(User $user, IepPlan $iepPlan): bool
    {
        return $user->hasPermission('iep.download_pdf');
    }

    public function reopen(User $user, IepPlan $iepPlan): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('iep.update');
    }

    public function delete(User $user, IepPlan $iepPlan): bool
    {
        return $user->role?->name !== 'supervisor'
            && $user->hasPermission('iep.update');
    }
}
