<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\File;
use App\Models\IepPlan;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Announcement;
use App\Models\Portfolio;
use App\Models\AuditLog;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentReport;
use App\Models\SupervisorVisit;
use App\Models\User;
use App\Policies\FilePolicy;
use App\Policies\IepPlanPolicy;
use App\Policies\MessagePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\PortfolioPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\SchoolPolicy;
use App\Policies\StudentPolicy;
use App\Policies\StudentReportPolicy;
use App\Policies\SupervisorVisitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        School::class => SchoolPolicy::class,
        User::class => UserPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
        Student::class => StudentPolicy::class,
        IepPlan::class => IepPlanPolicy::class,
        File::class => FilePolicy::class,
        Portfolio::class => PortfolioPolicy::class,
        StudentReport::class => StudentReportPolicy::class,
        SupervisorVisit::class => SupervisorVisitPolicy::class,
        Message::class => MessagePolicy::class,
        Notification::class => NotificationPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
