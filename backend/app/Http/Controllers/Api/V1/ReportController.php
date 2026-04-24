<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    public function schoolSummary(Request $request, School $school): JsonResponse
    {
        $this->ensurePermission($request, 'reports.school_summary');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تقرير المدرسة',
            'data' => $this->reportService->schoolSummary($school, $request->user()),
        ]);
    }

    public function studentSummary(Request $request, Student $student): JsonResponse
    {
        $this->ensurePermission($request, 'reports.student_summary');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تقرير الطالب',
            'data' => $this->reportService->studentSummary($student, $request->user()),
        ]);
    }

    public function comparison(Request $request): JsonResponse
    {
        $this->ensurePermission($request, 'reports.comparison');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تقرير المقارنة',
            'data' => $this->reportService->comparison($request->user(), $request->query()),
        ]);
    }

    public function pivot(Request $request): JsonResponse
    {
        $this->ensurePermission($request, 'reports.pivot');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب التقرير المحوري',
            'data' => $this->reportService->pivot($request->user(), $request->query()),
        ]);
    }

    public function exportPdf(Request $request): JsonResponse
    {
        $this->ensurePermission($request, 'reports.export_pdf');

        return response()->json([
            'success' => true,
            'message' => 'تم تجهيز معاينة تصدير PDF',
            'data' => $this->reportService->export('pdf', $request->user(), $request->query()),
        ]);
    }

    public function exportExcel(Request $request): JsonResponse
    {
        $this->ensurePermission($request, 'reports.export_excel');

        return response()->json([
            'success' => true,
            'message' => 'تم تجهيز معاينة تصدير Excel',
            'data' => $this->reportService->export('excel', $request->user(), $request->query()),
        ]);
    }

    private function ensurePermission(Request $request, string $permission): void
    {
        $user = $request->user();

        if ($user === null || ! $user->hasPermission($permission)) {
            throw new AuthorizationException('لا تملك صلاحية تنفيذ هذا التقرير.');
        }
    }
}
