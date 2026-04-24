<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);
        $notifications = $this->notificationService->listForCurrentUser($request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإشعارات',
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        $this->authorize('markRead', $notification);
        $notification = $this->notificationService->markRead($notification, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم تعليم الإشعار كمقروء',
            'data' => new NotificationResource($notification),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->authorize('markRead', Notification::class);
        $updated = $this->notificationService->markAllRead($request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم تعليم جميع الإشعارات كمقروءة',
            'data' => [
                'updated_count' => $updated,
            ],
        ]);
    }
}
