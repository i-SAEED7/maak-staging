<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserStatusRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $filters = $request->input('filter', []);
        if ($request->filled('per_page')) {
            $filters['per_page'] = $request->integer('per_page');
        }

        $users = $this->userService->paginate($filters);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المستخدمين',
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);
        $user = $this->userService->create($request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $user,
            [],
            $user->only(['full_name', 'email', 'school_id', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المستخدم',
            'data' => new UserResource($user->loadMissing('role', 'school', 'assignedSchools')),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تفاصيل المستخدم',
            'data' => new UserResource($user->loadMissing('role', 'school', 'assignedSchools')),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $before = $user->only(['full_name', 'email', 'school_id', 'status']);
        $user = $this->userService->update($user, $request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $user,
            $before,
            $user->only(['full_name', 'email', 'school_id', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستخدم',
            'data' => new UserResource($user->loadMissing('role', 'school', 'assignedSchools')),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('changeStatus', $user);
        $before = $user->only(['status']);
        $user = $this->userService->deactivate($user);
        app(AuditLogger::class)->log(
            request()->user(),
            'delete',
            $user,
            $before,
            $user->only(['status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل المستخدم',
            'data' => new UserResource($user),
        ]);
    }

    public function changeStatus(ChangeUserStatusRequest $request, User $user): JsonResponse
    {
        $this->authorize('changeStatus', $user);
        $before = $user->only(['status']);
        $user = $this->userService->changeStatus($user, $request->validated('status'));
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $user,
            $before,
            $user->only(['status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المستخدم',
            'data' => new UserResource($user->loadMissing('role', 'school', 'assignedSchools')),
        ]);
    }

    public function assignSchools(Request $request, User $user): JsonResponse
    {
        $this->authorize('assignSchools', $user);
        $schoolIds = $request->input('school_ids', []);
        $assignmentType = $user->role?->name === 'supervisor' ? 'supervising' : 'supporting';
        $this->userService->assignSchools($user, is_array($schoolIds) ? $schoolIds : [], $assignmentType);
        app(AuditLogger::class)->log($request->user(), 'update', $user, [], [
            'assigned_school_ids' => is_array($schoolIds) ? $schoolIds : [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إسناد المدارس',
        ]);
    }
}
