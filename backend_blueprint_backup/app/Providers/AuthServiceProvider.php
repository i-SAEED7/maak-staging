<?php

declare(strict_types=1);

namespace App\Providers;

final class AuthServiceProvider
{
    public function boot(): void
    {
        // Register policies:
        // School -> SchoolPolicy
        // User -> UserPolicy
        // Student -> StudentPolicy
        // IepPlan -> IepPlanPolicy
        // File -> FilePolicy
        // Portfolio -> PortfolioPolicy
        // StudentReport -> StudentReportPolicy
        // SupervisorVisit -> SupervisorVisitPolicy
        // Message -> MessagePolicy
    }
}
