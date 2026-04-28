<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\File;
use App\Models\FileAccessToken;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class FileUploadService
{
    public function __construct(
        private readonly TenantService $tenantService,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return File::query()
            ->with(['uploader', 'school'])
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($filters['visibility'] ?? null, fn ($query, $value) => $query->where('visibility', $value))
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', (int) $value))
            ->when($filters['search'] ?? null, function ($query, $value): void {
                $query->where(function ($fileQuery) use ($value): void {
                    $fileQuery
                        ->where('original_name', 'like', "%{$value}%")
                        ->orWhere('storage_name', 'like', "%{$value}%")
                        ->orWhere('mime_type', 'like', "%{$value}%")
                        ->orWhereHas('uploader', fn ($uploaderQuery) => $uploaderQuery->where('full_name', 'like', "%{$value}%"))
                        ->orWhereHas('school', fn ($schoolQuery) => $schoolQuery->where('name_ar', 'like', "%{$value}%"));
                });
            })
            ->latest('uploaded_at')
            ->latest('id')
            ->paginate(15);
    }

    public function upload(array $data, UploadedFile $uploadedFile, User $actor): File
    {
        $schoolId = $this->tenantContext->schoolId() ?? ($actor->school_id !== null ? (int) $actor->school_id : null);

        if ($schoolId === null) {
            throw ValidationException::withMessages([
                'school_id' => ['تعذر تحديد المدرسة الحالية لرفع الملف.'],
            ]);
        }

        $uuid = (string) Str::uuid();
        $extension = $uploadedFile->getClientOriginalExtension() ?: $uploadedFile->guessExtension();
        $storageName = $extension ? "{$uuid}.{$extension}" : $uuid;
        $directory = sprintf('maak/%d/%s', $schoolId, now()->format('Y/m'));
        $storagePath = $uploadedFile->storeAs($directory, $storageName, [
            'disk' => 'local',
        ]);

        if ($storagePath === false) {
            throw ValidationException::withMessages([
                'file' => ['تعذر حفظ الملف في وحدة التخزين الحالية.'],
            ]);
        }

        $checksum = $uploadedFile->getRealPath() !== false
            ? hash_file('sha256', $uploadedFile->getRealPath())
            : hash('sha256', (string) file_get_contents($uploadedFile->path()));

        $file = File::query()->create([
            'uuid' => $uuid,
            'school_id' => $schoolId,
            'uploaded_by_user_id' => $actor->id,
            'related_type' => $data['related_type'] ?? null,
            'related_id' => $data['related_id'] ?? null,
            'category' => $data['category'],
            'original_name' => $uploadedFile->getClientOriginalName(),
            'storage_name' => $storageName,
            'storage_disk' => 'local',
            'storage_path' => $storagePath,
            'mime_type' => $uploadedFile->getClientMimeType() ?: 'application/octet-stream',
            'extension' => $extension ?: null,
            'size_bytes' => (int) ($uploadedFile->getSize() ?: 0),
            'checksum_sha256' => $checksum ?: null,
            'is_sensitive' => (bool) ($data['is_sensitive'] ?? false),
            'visibility' => $data['visibility'] ?? 'private',
            'uploaded_at' => now(),
        ]);

        return $this->loadFile($file);
    }

    public function loadFile(File $file): File
    {
        return $file->load(['uploader', 'school']);
    }

    public function assertAccessible(File $file, User $actor): void
    {
        $this->tenantService->assertCanAccessSchoolId($actor, $file->school_id !== null ? (int) $file->school_id : null);
    }

    public function createTemporaryLink(File $file, User $actor, int $minutes = 30): array
    {
        $this->assertAccessible($file, $actor);

        $plainToken = Str::random(64);
        $token = FileAccessToken::query()->create([
            'file_id' => $file->id,
            'token_hash' => hash('sha256', $plainToken),
            'issued_to_user_id' => $actor->id,
            'expires_at' => now()->addMinutes($minutes),
            'consumed_at' => null,
            'created_at' => now(),
        ]);

        return [
            'token' => $plainToken,
            'url' => url("/temporary-files/{$plainToken}"),
            'preview_url' => url("/temporary-files/{$plainToken}?preview=1"),
            'expires_at' => $token->expires_at?->toAtomString(),
            'minutes' => $minutes,
        ];
    }

    public function delete(File $file, User $actor): File
    {
        $this->assertAccessible($file, $actor);

        if ($file->deleted_at === null) {
            $file->delete();
        }

        return File::withTrashed()
            ->with('uploader')
            ->findOrFail($file->id);
    }

    public function resolveDownloadByToken(string $plainToken): array
    {
        $token = FileAccessToken::query()
            ->with('file')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if ($token === null || $token->file === null) {
            throw ValidationException::withMessages([
                'token' => ['رابط التحميل المؤقت غير صالح.'],
            ]);
        }

        if ($token->consumed_at !== null) {
            throw ValidationException::withMessages([
                'token' => ['تم استخدام رابط التحميل المؤقت مسبقًا.'],
            ]);
        }

        if ($token->expires_at === null || $token->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'token' => ['انتهت صلاحية رابط التحميل المؤقت.'],
            ]);
        }

        $file = $token->file;

        if ($file->deleted_at !== null) {
            throw ValidationException::withMessages([
                'token' => ['الملف المطلوب لم يعد متاحًا.'],
            ]);
        }

        if (! Storage::disk($file->storage_disk)->exists($file->storage_path)) {
            throw ValidationException::withMessages([
                'token' => ['ملف التخزين غير موجود حاليًا.'],
            ]);
        }

        DB::transaction(function () use ($token): void {
            $token->update([
                'consumed_at' => now(),
            ]);
        });

        return [
            'file' => $file,
            'headers' => [
                'Content-Type' => $file->mime_type,
            ],
        ];
    }
}
