<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\AuditLog;
use App\Models\User;

final class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->name === RoleName::SUPER_ADMIN
            && $user->hasPermission('audit_logs.view_any');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $this->viewAny($user);
    }
}
