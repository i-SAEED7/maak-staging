<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AccessControlController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationProgramController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\IepPlanController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\SupervisorVisitController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\PortalController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::prefix('auth')->group(function (): void {
    Route::post('school-login', [AuthController::class, 'schoolLogin']);
    Route::post('central-login', [AuthController::class, 'centralLogin']);
});
Route::prefix('portal')->group(function (): void {
    Route::get('programs', [PortalController::class, 'programs']);
    Route::get('schools', [PortalController::class, 'schools']);
    Route::get('statistics', [PortalController::class, 'statistics']);
});

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('school-login', [AuthController::class, 'schoolLogin']);
        Route::post('central-login', [AuthController::class, 'centralLogin']);
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('schools', SchoolController::class);
        Route::patch('schools/{school}/status', [SchoolController::class, 'changeStatus']);
        Route::get('schools/{school}/stats', [SchoolController::class, 'stats']);
        Route::post('schools/{school}/assign-principal', [SchoolController::class, 'assignPrincipal']);
        Route::post('schools/{school}/assign-supervisor', [SchoolController::class, 'assignSupervisor']);

        Route::apiResource('users', UserController::class);
        Route::patch('users/{user}/status', [UserController::class, 'changeStatus']);
        Route::post('users/{user}/schools', [UserController::class, 'assignSchools']);
        Route::get('access-control/roles', [AccessControlController::class, 'roles']);
        Route::get('access-control/roles/{role}/users', [AccessControlController::class, 'roleUsers']);
        Route::get('access-control/permissions', [AccessControlController::class, 'permissions']);
        Route::put('access-control/roles/{role}/permissions', [AccessControlController::class, 'updateRolePermissions']);
        Route::put('access-control/roles/{role}/user-permissions', [AccessControlController::class, 'updateUserPermissions']);
        Route::get('dashboard/summary', [DashboardController::class, 'summary']);
        Route::get('audit-logs', [AuditLogController::class, 'index']);
        Route::get('education-programs', [EducationProgramController::class, 'index']);
        Route::post('education-programs', [EducationProgramController::class, 'store']);
        Route::put('education-programs/{educationProgram}', [EducationProgramController::class, 'update']);
        Route::delete('education-programs/{educationProgram}', [EducationProgramController::class, 'destroy']);

        Route::get('reports/schools/{school}/summary', [ReportController::class, 'schoolSummary']);
        Route::get('reports/students/{student}/summary', [ReportController::class, 'studentSummary']);
        Route::get('reports/comparison', [ReportController::class, 'comparison']);
        Route::get('reports/pivot', [ReportController::class, 'pivot']);
        Route::get('reports/export/pdf', [ReportController::class, 'exportPdf']);
        Route::get('reports/export/excel', [ReportController::class, 'exportExcel']);

        Route::middleware('tenant:required')->group(function (): void {
            Route::apiResource('students', StudentController::class)->except(['destroy']);
            Route::patch('students/{student}/archive', [StudentController::class, 'archive']);
            Route::get('students/{student}/guardians', [StudentController::class, 'guardians']);
            Route::post('students/{student}/guardians', [StudentController::class, 'assignGuardian']);

            Route::apiResource('iep-plans', IepPlanController::class)
                ->parameters(['iep-plans' => 'iepPlan'])
                ->except([]);
            Route::post('iep-plans/{iepPlan}/submit', [IepPlanController::class, 'submit']);
            Route::post('iep-plans/{iepPlan}/principal-approve', [IepPlanController::class, 'principalApprove']);
            Route::post('iep-plans/{iepPlan}/supervisor-approve', [IepPlanController::class, 'supervisorApprove']);
            Route::post('iep-plans/{iepPlan}/acknowledge', [IepPlanController::class, 'acknowledge']);
            Route::post('iep-plans/{iepPlan}/reject', [IepPlanController::class, 'reject']);
            Route::post('iep-plans/{iepPlan}/reopen', [IepPlanController::class, 'reopen']);
            Route::get('iep-plans/{iepPlan}/versions', [IepPlanController::class, 'versions']);
            Route::post('iep-plans/{iepPlan}/comments', [IepPlanController::class, 'comment']);
            Route::get('iep-plans/{iepPlan}/pdf', [IepPlanController::class, 'pdf']);

            Route::get('messages', [MessageController::class, 'index']);
            Route::get('messages/recipients', [MessageController::class, 'recipients']);
            Route::get('messages/thread/{threadKey}', [MessageController::class, 'thread']);
            Route::post('messages', [MessageController::class, 'store']);
            Route::post('messages/{message}/read', [MessageController::class, 'markRead']);

            Route::get('notifications', [NotificationController::class, 'index']);
            Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
            Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);

            Route::get('files', [FileController::class, 'index']);
            Route::post('files', [FileController::class, 'store']);
            Route::get('files/{file}', [FileController::class, 'show']);
            Route::post('files/{file}/temporary-link', [FileController::class, 'temporaryLink']);
            Route::delete('files/{file}', [FileController::class, 'destroy']);

            Route::apiResource('supervisor-visits', SupervisorVisitController::class)
                ->parameters(['supervisor-visits' => 'supervisorVisit'])
                ->only(['index', 'store', 'show', 'update']);
            Route::post('supervisor-visits/{supervisorVisit}/complete', [SupervisorVisitController::class, 'complete']);
            Route::post('supervisor-visits/{supervisorVisit}/recommendations', [SupervisorVisitController::class, 'addRecommendation']);
            Route::patch('supervisor-visit-recommendations/{recommendation}', [SupervisorVisitController::class, 'updateRecommendation']);
        });

        Route::middleware('tenant')->group(function (): void {
            Route::get('announcements', [AnnouncementController::class, 'index']);
            Route::get('announcements/{announcement}', [AnnouncementController::class, 'show']);
            Route::post('announcements', [AnnouncementController::class, 'store']);
            Route::put('announcements/{announcement}', [AnnouncementController::class, 'update']);
            Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy']);
        });
    });
});
