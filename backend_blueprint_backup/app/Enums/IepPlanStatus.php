<?php

declare(strict_types=1);

namespace App\Enums;

final class IepPlanStatus
{
    public const DRAFT = 'draft';
    public const PENDING_PRINCIPAL_REVIEW = 'pending_principal_review';
    public const PENDING_SUPERVISOR_REVIEW = 'pending_supervisor_review';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const ARCHIVED = 'archived';
}
