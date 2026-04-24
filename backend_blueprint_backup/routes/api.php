<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| API Routes Blueprint
|--------------------------------------------------------------------------
|
| Replace these comments with concrete Laravel Route definitions once the
| framework is installed. The grouping below mirrors docs/BACKEND_ROUTE_MAP.md
| and docs/API_SPEC.md.
|
| Route::prefix('v1')->group(function (): void {
|     Route::prefix('auth')->group(function (): void {
|         Route::post('login', [AuthController::class, 'login']);
|         Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
|         Route::post('verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
|         Route::post('reset-password', [AuthController::class, 'resetPassword']);
|     });
|
|     Route::middleware('auth:sanctum')->group(function (): void {
|         Route::post('auth/logout', [AuthController::class, 'logout']);
|         Route::get('auth/me', [AuthController::class, 'me']);
|         Route::post('auth/change-password', [AuthController::class, 'changePassword']);
|
|         Route::apiResource('schools', SchoolController::class)->except(['destroy']);
|         Route::patch('schools/{school}/status', [SchoolController::class, 'changeStatus']);
|         Route::get('schools/{school}/stats', [SchoolController::class, 'stats']);
|         Route::post('schools/{school}/assign-principal', [SchoolController::class, 'assignPrincipal']);
|         Route::post('schools/{school}/assign-supervisor', [SchoolController::class, 'assignSupervisor']);
|
|         Route::apiResource('users', UserController::class)->except(['destroy']);
|         Route::patch('users/{user}/status', [UserController::class, 'changeStatus']);
|         Route::post('users/{user}/schools', [UserController::class, 'assignSchools']);
|
|         Route::apiResource('students', StudentController::class)->except(['destroy']);
|         Route::patch('students/{student}/archive', [StudentController::class, 'archive']);
|         Route::get('students/{student}/guardians', [StudentController::class, 'guardians']);
|         Route::post('students/{student}/guardians', [StudentController::class, 'assignGuardian']);
|
|         Route::get('teachers', [TeacherController::class, 'index']);
|         Route::get('teachers/{teacher}/students', [TeacherController::class, 'students']);
|
|         Route::get('portfolios', [PortfolioController::class, 'index']);
|         Route::post('portfolios', [PortfolioController::class, 'store']);
|         Route::get('portfolios/{portfolio}', [PortfolioController::class, 'show']);
|         Route::post('portfolios/{portfolio}/items', [PortfolioController::class, 'storeItem']);
|         Route::put('portfolio-items/{portfolioItem}', [PortfolioController::class, 'updateItem']);
|         Route::delete('portfolio-items/{portfolioItem}', [PortfolioController::class, 'deleteItem']);
|
|         Route::get('iep-plans', [IepPlanController::class, 'index']);
|         Route::post('iep-plans', [IepPlanController::class, 'store']);
|         Route::get('iep-plans/{iepPlan}', [IepPlanController::class, 'show']);
|         Route::put('iep-plans/{iepPlan}', [IepPlanController::class, 'update']);
|         Route::post('iep-plans/{iepPlan}/submit', [IepPlanController::class, 'submit']);
|         Route::post('iep-plans/{iepPlan}/principal-approve', [IepPlanController::class, 'principalApprove']);
|         Route::post('iep-plans/{iepPlan}/supervisor-approve', [IepPlanController::class, 'supervisorApprove']);
|         Route::post('iep-plans/{iepPlan}/reject', [IepPlanController::class, 'reject']);
|         Route::get('iep-plans/{iepPlan}/versions', [IepPlanController::class, 'versions']);
|         Route::post('iep-plans/{iepPlan}/comments', [IepPlanController::class, 'comment']);
|         Route::get('iep-plans/{iepPlan}/pdf', [IepPlanController::class, 'pdf']);
|
|         Route::get('student-reports', [StudentReportController::class, 'index']);
|         Route::post('student-reports', [StudentReportController::class, 'store']);
|         Route::get('student-reports/{studentReport}', [StudentReportController::class, 'show']);
|         Route::put('student-reports/{studentReport}', [StudentReportController::class, 'update']);
|         Route::post('student-reports/{studentReport}/publish', [StudentReportController::class, 'publish']);
|
|         Route::post('files', [FileController::class, 'store']);
|         Route::get('files/{file}', [FileController::class, 'show']);
|         Route::post('files/{file}/temporary-link', [FileController::class, 'temporaryLink']);
|         Route::delete('files/{file}', [FileController::class, 'destroy']);
|
|         Route::get('supervisor-visits', [SupervisorVisitController::class, 'index']);
|         Route::post('supervisor-visits', [SupervisorVisitController::class, 'store']);
|         Route::get('supervisor-visits/{visit}', [SupervisorVisitController::class, 'show']);
|         Route::put('supervisor-visits/{visit}', [SupervisorVisitController::class, 'update']);
|         Route::post('supervisor-visits/{visit}/complete', [SupervisorVisitController::class, 'complete']);
|         Route::post('supervisor-visits/{visit}/recommendations', [SupervisorVisitController::class, 'addRecommendation']);
|         Route::patch('supervisor-visit-recommendations/{recommendation}', [SupervisorVisitController::class, 'updateRecommendation']);
|
|         Route::get('notifications', [NotificationController::class, 'index']);
|         Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
|         Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
|
|         Route::get('messages', [MessageController::class, 'index']);
|         Route::get('messages/thread/{threadKey}', [MessageController::class, 'thread']);
|         Route::post('messages', [MessageController::class, 'store']);
|         Route::post('messages/{message}/read', [MessageController::class, 'markRead']);
|
|         Route::get('reports/schools/{school}/summary', [ReportController::class, 'schoolSummary']);
|         Route::get('reports/students/{student}/summary', [ReportController::class, 'studentSummary']);
|         Route::get('reports/comparison', [ReportController::class, 'comparison']);
|         Route::get('reports/pivot', [ReportController::class, 'pivot']);
|         Route::get('reports/export/pdf', [ReportController::class, 'exportPdf']);
|         Route::get('reports/export/excel', [ReportController::class, 'exportExcel']);
|     });
| });
|
*/
