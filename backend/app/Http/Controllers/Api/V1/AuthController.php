<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AuthPortalException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Support\PermissionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly PermissionResolver $permissionResolver,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated(), $request);
        } catch (AuthPortalException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($result, 'تم تسجيل الدخول');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $this->authService->logout($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()?->loadMissing('role.permissions', 'school', 'assignedSchools');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المستخدم الحالي',
            'data' => [
                'user' => $user ? new UserResource($user) : null,
                'permissions' => $user ? $this->permissionResolver->resolveForUser($user) : [],
            ],
        ]);
    }

    private function successResponse(array $result, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'token' => $result['token'],
                'user' => new UserResource($result['user']),
                'permissions' => $result['permissions'],
            ],
        ]);
    }

    private function errorResponse(AuthPortalException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], $exception->status);
    }
}
