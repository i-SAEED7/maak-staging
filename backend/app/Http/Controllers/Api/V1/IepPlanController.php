<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentIepPlanRequest;
use App\Http\Requests\StoreIepPlanRequest;
use App\Http\Requests\TransitionIepPlanRequest;
use App\Http\Requests\UpdateIepPlanRequest;
use App\Http\Resources\IepPlanResource;
use App\Models\IepPlan;
use App\Services\IepPlanService;
use App\Services\IepWorkflowService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class IepPlanController extends Controller
{
    public function __construct(
        private readonly IepPlanService $iepPlanService,
        private readonly IepWorkflowService $iepWorkflowService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', IepPlan::class);
        $plans = $this->iepPlanService->paginate($request->input('filter', []));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الخطط الفردية',
            'data' => IepPlanResource::collection($plans->items()),
            'meta' => [
                'page' => $plans->currentPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
                'last_page' => $plans->lastPage(),
            ],
        ]);
    }

    public function store(StoreIepPlanRequest $request): JsonResponse
    {
        $this->authorize('create', IepPlan::class);
        $plan = $this->iepPlanService->create($request->validated(), $request->user());
        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $plan,
            [],
            $plan->only(['title', 'status', 'current_version_number', 'school_id', 'student_id']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الخطة الفردية',
            'data' => new IepPlanResource($plan),
        ], 201);
    }

    public function show(Request $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('view', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تفاصيل الخطة',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($iepPlan)),
        ]);
    }

    public function update(UpdateIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('update', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $before = $iepPlan->only(['title', 'status', 'current_version_number', 'school_id', 'student_id']);
        $plan = $this->iepPlanService->update($iepPlan, $request->validated(), $request->user());
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $plan,
            $before,
            $plan->only(['title', 'status', 'current_version_number', 'school_id', 'student_id']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الخطة الفردية',
            'data' => new IepPlanResource($plan),
        ]);
    }

    public function submit(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('submit', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->submit($iepPlan, $request->user(), $request->validated('notes'));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الخطة لمراجعة المدير',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function principalApprove(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('principalApprove', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->principalApprove($iepPlan, $request->user(), $request->validated('notes'));

        return response()->json([
            'success' => true,
            'message' => 'تم اعتماد الخطة من مدير المدرسة',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function supervisorApprove(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('supervisorApprove', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->supervisorApprove($iepPlan, $request->user(), $request->validated('notes'));

        return response()->json([
            'success' => true,
            'message' => 'تم اعتماد الخطة نهائيًا',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function acknowledge(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('acknowledge', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->acknowledge($iepPlan, $request->user(), $request->validated('notes'));

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل إقرار ولي الأمر بالاطلاع',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function reject(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('reject', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->reject(
            $iepPlan,
            $request->user(),
            (string) $request->validated('reason'),
            $request->validated('notes'),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الخطة',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function reopen(TransitionIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('reopen', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepWorkflowService->reopen($iepPlan, $request->user(), $request->validated('notes'));

        return response()->json([
            'success' => true,
            'message' => 'تمت إعادة الخطة إلى مسودة',
            'data' => new IepPlanResource($this->iepPlanService->loadPlan($plan)),
        ]);
    }

    public function versions(Request $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('versions', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $plan = $this->iepPlanService->versions($iepPlan);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب إصدارات الخطة',
            'data' => (new IepPlanResource($plan))->toArray($request)['versions'],
        ]);
    }

    public function comment(CommentIepPlanRequest $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('comment', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $comment = $this->iepPlanService->addComment($iepPlan, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة التعليق',
            'data' => [
                'id' => $comment->id,
                'author_user_id' => $comment->author_user_id,
                'author_name' => $comment->author?->full_name,
                'target_section' => $comment->target_section,
                'comment_text' => $comment->comment_text,
                'is_internal' => $comment->is_internal,
                'created_at' => $comment->created_at?->toAtomString(),
            ],
        ], 201);
    }

    public function pdf(Request $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('pdf', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب حالة ملف PDF للخطة',
            'data' => $this->iepPlanService->pdfPayload($iepPlan),
        ]);
    }

    public function destroy(Request $request, IepPlan $iepPlan): JsonResponse
    {
        $this->authorize('delete', $iepPlan);
        $this->iepPlanService->assertAccessible($iepPlan, $request->user());
        $before = $iepPlan->only(['title', 'status', 'current_version_number', 'school_id', 'student_id']);
        $this->iepPlanService->delete($iepPlan, $request->user());
        app(AuditLogger::class)->log($request->user(), 'delete', $iepPlan, $before, [
            'deleted_at' => now()->toAtomString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخطة الفردية منطقيًا',
            'data' => [
                'id' => $iepPlan->id,
                'deleted_at' => now()->toAtomString(),
            ],
        ]);
    }
}
