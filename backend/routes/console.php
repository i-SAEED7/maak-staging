<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('audit-logs:archive', function () {
    $count = app(\App\Services\AuditLogService::class)->archiveOlderThanThreeMonths();

    $this->info("Archived {$count} audit log record(s).");
})->purpose('Archive audit logs older than three months');

Schedule::command('audit-logs:archive')->daily();
