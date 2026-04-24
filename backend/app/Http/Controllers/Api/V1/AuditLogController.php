<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AuditLog::class);

        $filters = $request->input('filter', []);
        if ($request->filled('per_page')) {
            $filters['per_page'] = $request->integer('per_page');
        }

        $logs = $this->auditLogService->paginate($request->user(), $filters);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سجل التعديلات',
            'data' => AuditLogResource::collection($logs->items()),
            'meta' => [
                'page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage(),
            ],
        ]);
    }
}
