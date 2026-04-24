<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\IepPlanApproved;
use App\Events\IepPlanSubmitted;
use App\Events\MessageSent;
use App\Events\StudentReportPublished;
use App\Events\SupervisorVisitCompleted;
use App\Listeners\NotifyGuardiansOfReport;
use App\Listeners\QueueIepPdfGeneration;
use App\Listeners\SendWorkflowNotification;
use App\Listeners\StoreAuditLogEntry;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        IepPlanSubmitted::class => [
            SendWorkflowNotification::class,
            StoreAuditLogEntry::class,
        ],
        IepPlanApproved::class => [
            QueueIepPdfGeneration::class,
            SendWorkflowNotification::class,
            StoreAuditLogEntry::class,
        ],
        StudentReportPublished::class => [
            NotifyGuardiansOfReport::class,
            StoreAuditLogEntry::class,
        ],
        MessageSent::class => [
            SendWorkflowNotification::class,
        ],
        SupervisorVisitCompleted::class => [
            SendWorkflowNotification::class,
            StoreAuditLogEntry::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
