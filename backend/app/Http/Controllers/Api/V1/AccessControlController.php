<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Http\Requests\UpdateUserPermissionsRequest;
use App\Models\Role;
use App\Services\AccessControlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccessControlController extends Controller
{
    public function __construct(
        private readonly AccessControlService $accessControlService,
    ) {
    }

    public function roles(Request $request): JsonResponse
    {
        $this->assertSuperAdmin($request);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الأدوار وصلاحياتها',
            'data' => $this->accessControlService->roles(),
        ]);
    }

    public function permissions(Request $request): JsonResponse
    {
        $this->assertSuperAdmin($request);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الصلاحيات',
            'data' => $this->accessControlService->permissions(),
        ]);
    }

    public function roleUsers(Request $request, Role $role): JsonResponse
    {
        $this->assertSuperAdmin($request);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب حسابات الدور',
            'data' => $this->accessControlService->usersForRole($role),
        ]);
    }

    public function updateRolePermissions(
        UpdateRolePermissionsRequest $request,
        Role $role,
    ): JsonResponse {
        $this->assertSuperAdmin($request);
        $role = $this->accessControlService->updateRolePermissions(
            $role,
            $request->validated('permission_keys'),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صلاحيات الدور',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name_ar' => $role->display_name_ar,
                'permissions' => $role->permissions
                    ->map(static fn ($permission): array => [
                        'id' => $permission->id,
                        'key' => $permission->key,
                        'display_name_ar' => $permission->display_name_ar,
                        'module' => $permission->module,
                ])
                    ->values()
                    ->all(),
            ],
        ]);
    }

    public function updateUserPermissions(
        UpdateUserPermissionsRequest $request,
        Role $role,
    ): JsonResponse {
        $this->assertSuperAdmin($request);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صلاحيات الحسابات المحددة',
            'data' => $this->accessControlService->updateUserPermissions(
                $role,
                $request->validated('permission_keys'),
                $request->validated('user_ids', []),
                (bool) $request->validated('apply_to_all', false),
            ),
        ]);
    }

    private function assertSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role?->name === 'super_admin', 403);
    }
}
