<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'تم جلب مؤشرات لوحة التحكم',
            'data' => $this->dashboardService->summaryWithFilters($request->user(), [
                'school_id' => $request->query('school_id'),
                'program_type' => $request->query('program_type'),
            ]),
        ]);
    }
}
