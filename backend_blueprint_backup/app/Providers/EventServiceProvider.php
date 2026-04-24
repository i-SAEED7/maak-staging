<?php

declare(strict_types=1);

namespace App\Providers;

final class EventServiceProvider
{
    public function boot(): void
    {
        // Map domain events to listeners:
        // IepPlanSubmitted => SendWorkflowNotification, StoreAuditLogEntry
        // IepPlanApproved => QueueIepPdfGeneration, SendWorkflowNotification, StoreAuditLogEntry
        // StudentReportPublished => NotifyGuardiansOfReport, StoreAuditLogEntry
        // MessageSent => SendWorkflowNotification
        // SupervisorVisitCompleted => SendWorkflowNotification, StoreAuditLogEntry
    }
}
