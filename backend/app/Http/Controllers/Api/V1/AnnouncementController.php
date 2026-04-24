<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcementService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Announcement::class);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإعلانات',
            'data' => AnnouncementResource::collection($this->announcementService->listFor($request->user())),
        ]);
    }

    public function show(Request $request, Announcement $announcement): JsonResponse
    {
        $this->authorize('view', $announcement);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإعلان',
            'data' => new AnnouncementResource($this->announcementService->findFor($request->user(), $announcement)),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): JsonResponse
    {
        $this->authorize('create', Announcement::class);
        $announcement = $this->announcementService->create($request->validated(), $request->user());
        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $announcement,
            [],
            $announcement->only(['title', 'target_audience', 'school_id', 'is_all_schools', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الإعلان',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): JsonResponse
    {
        $this->authorize('update', $announcement);
        $before = $announcement->only(['title', 'target_audience', 'school_id', 'is_all_schools', 'status']);
        $announcement = $this->announcementService->update($announcement, $request->validated(), $request->user());
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $announcement,
            $before,
            $announcement->only(['title', 'target_audience', 'school_id', 'is_all_schools', 'status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعلان',
            'data' => new AnnouncementResource($announcement),
        ]);
    }

    public function destroy(Request $request, Announcement $announcement): JsonResponse
    {
        $this->authorize('delete', $announcement);
        $before = $announcement->only(['title', 'target_audience', 'school_id', 'is_all_schools', 'status']);
        $this->announcementService->delete($announcement);
        app(AuditLogger::class)->log($request->user(), 'delete', $announcement, $before, [
            'deleted_at' => now()->toAtomString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإعلان',
            'data' => [
                'id' => $announcement->id,
            ],
        ]);
    }
}
