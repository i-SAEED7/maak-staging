<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTemporaryFileLinkRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileUploadService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', File::class);
        $files = $this->fileUploadService->paginate($request->input('filter', []));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الملفات',
            'data' => FileResource::collection($files->items()),
            'meta' => [
                'page' => $files->currentPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
                'last_page' => $files->lastPage(),
            ],
        ]);
    }

    public function store(UploadFileRequest $request): JsonResponse
    {
        $this->authorize('upload', File::class);
        $file = $this->fileUploadService->upload(
            $request->safe()->except('file'),
            $request->file('file'),
            $request->user(),
        );
        app(AuditLogger::class)->log(
            $request->user(),
            'upload',
            $file,
            [],
            $file->only(['original_name', 'category', 'visibility', 'school_id', 'uploaded_by_user_id']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم رفع الملف',
            'data' => new FileResource($file),
        ], 201);
    }

    public function show(Request $request, File $file): JsonResponse
    {
        $this->authorize('view', $file);
        $this->fileUploadService->assertAccessible($file, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب بيانات الملف',
            'data' => new FileResource($this->fileUploadService->loadFile($file)),
        ]);
    }

    public function temporaryLink(CreateTemporaryFileLinkRequest $request, File $file): JsonResponse
    {
        $this->authorize('download', $file);
        $this->fileUploadService->assertAccessible($file, $request->user());
        $link = $this->fileUploadService->createTemporaryLink(
            $file,
            $request->user(),
            (int) ($request->validated('expires_in_minutes') ?? 30),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء رابط تحميل مؤقت',
            'data' => [
                'file' => new FileResource($this->fileUploadService->loadFile($file)),
                'temporary_link' => $link,
            ],
        ]);
    }

    public function destroy(Request $request, File $file): JsonResponse
    {
        $this->authorize('delete', $file);
        $before = $file->only(['original_name', 'category', 'visibility', 'school_id', 'uploaded_by_user_id']);
        $deletedFile = $this->fileUploadService->delete($file, $request->user());
        app(AuditLogger::class)->log($request->user(), 'delete', $deletedFile, $before, [
            'deleted_at' => $deletedFile->deleted_at?->toAtomString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف مرجع الملف منطقيًا',
            'data' => [
                'id' => $deletedFile->id,
                'deleted_at' => $deletedFile->deleted_at?->toAtomString(),
            ],
        ]);
    }

    public function downloadTemporary(string $token): StreamedResponse|BinaryFileResponse
    {
        $payload = $this->fileUploadService->resolveDownloadByToken($token);
        $file = $payload['file'];

        return Storage::disk($file->storage_disk)->download(
            $file->storage_path,
            $file->original_name,
            $payload['headers'],
        );
    }
}
