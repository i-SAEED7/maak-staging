<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPrincipalRequest;
use App\Http\Requests\AssignSupervisorRequest;
use App\Http\Requests\ChangeSchoolStatusRequest;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use App\Services\SchoolService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function __construct(
        private readonly SchoolService $schoolService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', School::class);
        $schools = $this->schoolService->paginate(
            $request->input('filter', []),
            (int) $request->integer('per_page', 10),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المدارس',
            'data' => SchoolResource::collection($schools->items()),
            'meta' => [
                'page' => $schools->currentPage(),
                'per_page' => $schools->perPage(),
                'total' => $schools->total(),
                'last_page' => $schools->lastPage(),
            ],
        ]);
    }

    public function store(StoreSchoolRequest $request): JsonResponse
    {
        $this->authorize('create', School::class);
        $school = $this->schoolService->create($request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $school,
            [],
            $school->only(['name_ar', 'ministry_code', 'stage', 'program_type', 'gender', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المدرسة',
            'data' => new SchoolResource($school),
        ], 201);
    }

    public function show(School $school): JsonResponse
    {
        $this->authorize('view', $school);
        $this->schoolService->assertAccessible($school, auth()->user());
        $school = $this->schoolService->load($school);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تفاصيل المدرسة',
            'data' => new SchoolResource($school),
        ]);
    }

    public function update(UpdateSchoolRequest $request, School $school): JsonResponse
    {
        $this->authorize('update', $school);
        $before = $school->only(['name_ar', 'ministry_code', 'stage', 'program_type', 'gender', 'status']);
        $school = $this->schoolService->update($school, $request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $school,
            $before,
            $school->only(['name_ar', 'ministry_code', 'stage', 'program_type', 'gender', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المدرسة',
            'data' => new SchoolResource($school),
        ]);
    }

    public function changeStatus(ChangeSchoolStatusRequest $request, School $school): JsonResponse
    {
        $this->authorize('changeStatus', $school);
        $before = $school->only(['status']);
        $school = $this->schoolService->changeStatus($school, $request->validated('status'));
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $school,
            $before,
            $school->only(['status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المدرسة',
            'data' => new SchoolResource($school),
        ]);
    }

    public function stats(School $school): JsonResponse
    {
        $this->authorize('stats', $school);
        $this->schoolService->assertAccessible($school, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب إحصاءات المدرسة',
            'data' => $this->schoolService->stats($school),
        ]);
    }

    public function assignPrincipal(AssignPrincipalRequest $request, School $school): JsonResponse
    {
        $this->authorize('assignPrincipal', $school);
        $before = $school->only(['principal_id', 'principal_user_id']);
        $school = $this->schoolService->assignPrincipal($school, (int) $request->validated('principal_user_id'));
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $school,
            $before,
            $school->only(['principal_id', 'principal_user_id']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إسناد مدير المدرسة',
            'data' => new SchoolResource($school),
        ]);
    }

    public function assignSupervisor(AssignSupervisorRequest $request, School $school): JsonResponse
    {
        $this->authorize('assignSupervisor', $school);
        $before = $school->only(['supervisor_id']);
        $school = $this->schoolService->assignSupervisor($school, (int) $request->validated('supervisor_user_id'));
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $school,
            $before,
            $school->only(['supervisor_id']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إسناد المشرف',
            'data' => new SchoolResource($school),
        ]);
    }

    public function destroy(School $school): JsonResponse
    {
        $this->authorize('delete', $school);
        $before = $school->only(['status']);
        $school = $this->schoolService->deactivate($school);
        app(AuditLogger::class)->log(
            request()->user(),
            'delete',
            $school,
            $before,
            $school->only(['status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل المدرسة',
            'data' => new SchoolResource($school),
        ]);
    }
}
