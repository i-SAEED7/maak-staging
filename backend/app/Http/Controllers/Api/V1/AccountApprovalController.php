<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountApprovalRequest;
use App\Http\Requests\UpdateAccountApprovalRequest;
use App\Http\Resources\AccountApprovalRequestResource;
use App\Models\AccountApprovalRequest;
use App\Services\AccountApprovalService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountApprovalController extends Controller
{
    public function __construct(
        private readonly AccountApprovalService $accountApprovalService,
    ) {
    }

    public function store(StoreAccountApprovalRequest $request): JsonResponse
    {
        $approval = $this->accountApprovalService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب التسجيل وسيتم مراجعته من السوبر أدمن.',
            'data' => new AccountApprovalRequestResource($approval),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $this->ensureSuperAdmin($request);

        $filters = $request->input('filter', []);
        if ($request->filled('per_page')) {
            $filters['per_page'] = $request->integer('per_page');
        }

        $approvals = $this->accountApprovalService->paginate($filters);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب طلبات اعتماد الحسابات',
            'data' => AccountApprovalRequestResource::collection($approvals->items()),
            'meta' => [
                'page' => $approvals->currentPage(),
                'per_page' => $approvals->perPage(),
                'total' => $approvals->total(),
                'last_page' => $approvals->lastPage(),
            ],
        ]);
    }

    public function update(UpdateAccountApprovalRequest $request, AccountApprovalRequest $accountApproval): JsonResponse
    {
        $this->ensureSuperAdmin($request);
        $before = $accountApproval->only(['first_name', 'second_name', 'last_name', 'email', 'phone', 'account_type', 'school_id', 'stage']);
        $approval = $this->accountApprovalService->update($accountApproval, $request->validated());
        app(AuditLogger::class)->log($request->user(), 'update', $approval, $before, [
            'first_name' => $approval->first_name,
            'second_name' => $approval->second_name,
            'last_name' => $approval->last_name,
            'email' => $approval->email,
            'phone' => $approval->phone,
            'account_type' => $approval->account_type,
            'school_id' => $approval->school_id,
            'stage' => $approval->stage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث طلب الاعتماد',
            'data' => new AccountApprovalRequestResource($approval),
        ]);
    }

    public function approve(Request $request, AccountApprovalRequest $accountApproval): JsonResponse
    {
        $this->ensureSuperAdmin($request);
        $approval = $this->accountApprovalService->approve($accountApproval, $request->user());
        app(AuditLogger::class)->log($request->user(), 'create', $approval->createdUser, [], [
            'account_approval_request_id' => $approval->id,
            'account_type' => $approval->account_type,
            'school_id' => $approval->school_id,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم اعتماد الحساب وتفعيله وربطه بالمدرسة.',
            'data' => new AccountApprovalRequestResource($approval),
        ]);
    }

    private function ensureSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role?->name === 'super_admin', 403, 'غير مصرح بتنفيذ هذا الإجراء.');
    }
}
