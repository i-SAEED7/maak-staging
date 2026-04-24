<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteSupervisorVisitRequest;
use App\Http\Requests\StoreSupervisorVisitRequest;
use App\Http\Requests\StoreVisitRecommendationRequest;
use App\Http\Requests\UpdateSupervisorVisitRequest;
use App\Http\Requests\UpdateVisitRecommendationRequest;
use App\Http\Resources\SupervisorVisitResource;
use App\Models\SupervisorVisit;
use App\Models\SupervisorVisitRecommendation;
use App\Services\SupervisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SupervisorVisitController extends Controller
{
    public function __construct(
        private readonly SupervisionService $supervisionService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SupervisorVisit::class);
        $visits = $this->supervisionService->paginateVisits($request->user(), $request->input('filter', []));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الزيارات الإشرافية',
            'data' => SupervisorVisitResource::collection($visits->items()),
            'meta' => [
                'page' => $visits->currentPage(),
                'per_page' => $visits->perPage(),
                'total' => $visits->total(),
            ],
        ]);
    }

    public function store(StoreSupervisorVisitRequest $request): JsonResponse
    {
        $this->authorize('create', SupervisorVisit::class);
        $visit = $this->supervisionService->createVisit($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جدولة الزيارة الإشرافية',
            'data' => new SupervisorVisitResource($visit),
        ], 201);
    }

    public function show(Request $request, SupervisorVisit $supervisorVisit): JsonResponse
    {
        $this->authorize('view', $supervisorVisit);
        $this->supervisionService->assertAccessible($supervisorVisit, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تفاصيل الزيارة',
            'data' => new SupervisorVisitResource($this->supervisionService->loadVisit($supervisorVisit)),
        ]);
    }

    public function update(UpdateSupervisorVisitRequest $request, SupervisorVisit $supervisorVisit): JsonResponse
    {
        $this->authorize('update', $supervisorVisit);
        $visit = $this->supervisionService->updateVisit($supervisorVisit, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الزيارة الإشرافية',
            'data' => new SupervisorVisitResource($visit),
        ]);
    }

    public function complete(CompleteSupervisorVisitRequest $request, SupervisorVisit $supervisorVisit): JsonResponse
    {
        $this->authorize('complete', $supervisorVisit);
        $visit = $this->supervisionService->completeVisit($supervisorVisit, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء الزيارة الإشرافية',
            'data' => new SupervisorVisitResource($visit),
        ]);
    }

    public function addRecommendation(StoreVisitRecommendationRequest $request, SupervisorVisit $supervisorVisit): JsonResponse
    {
        $this->authorize('addRecommendation', $supervisorVisit);
        $recommendation = $this->supervisionService->addRecommendation($supervisorVisit, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة التوصية',
            'data' => [
                'id' => $recommendation->id,
                'recommendation_text' => $recommendation->recommendation_text,
                'owner_user_id' => $recommendation->owner_user_id,
                'owner_name' => $recommendation->owner?->full_name,
                'due_date' => $recommendation->due_date?->toDateString(),
                'status' => $recommendation->status,
                'completed_at' => $recommendation->completed_at?->toAtomString(),
            ],
        ], 201);
    }

    public function updateRecommendation(UpdateVisitRecommendationRequest $request, SupervisorVisitRecommendation $recommendation): JsonResponse
    {
        $this->authorize('updateRecommendation', SupervisorVisit::class);
        $recommendation = $this->supervisionService->updateRecommendation($recommendation, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التوصية',
            'data' => [
                'id' => $recommendation->id,
                'recommendation_text' => $recommendation->recommendation_text,
                'owner_user_id' => $recommendation->owner_user_id,
                'owner_name' => $recommendation->owner?->full_name,
                'due_date' => $recommendation->due_date?->toDateString(),
                'status' => $recommendation->status,
                'completed_at' => $recommendation->completed_at?->toAtomString(),
            ],
        ]);
    }
}
