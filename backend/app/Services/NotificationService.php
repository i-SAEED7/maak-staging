<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class NotificationService
{
    public function __construct(
        private readonly TenantService $tenantService,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function listForCurrentUser(User $actor): Collection
    {
        return Notification::query()
            ->with(['user.role', 'creator.role', 'school'])
            ->when(
                $actor->role?->name !== 'super_admin',
                fn ($query) => $query->where('user_id', $actor->id),
            )
            ->latest()
            ->get();
    }

    public function send(array $payload): Notification
    {
        return Notification::query()->create([
            'uuid' => (string) Str::uuid(),
            'school_id' => $payload['school_id'] ?? $this->tenantContext->schoolId(),
            'user_id' => $payload['user_id'],
            'created_by_user_id' => $payload['created_by_user_id'] ?? $payload['user_id'],
            'type' => $payload['type'],
            'channel' => $payload['channel'] ?? 'in_app',
            'title' => $payload['title'],
            'body' => $payload['body'],
            'data' => $payload['data'] ?? null,
            'read_at' => $payload['read_at'] ?? null,
            'sent_at' => $payload['sent_at'] ?? now(),
            'failed_at' => $payload['failed_at'] ?? null,
        ]);
    }

    public function markRead(Notification $notification, User $actor): Notification
    {
        $this->assertAccessible($notification, $actor);

        if ((int) $notification->user_id !== (int) $actor->id) {
            throw ValidationException::withMessages([
                'notification_id' => ['هذا الإشعار غير مخصص للمستخدم الحالي.'],
            ]);
        }

        if ($notification->read_at === null) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return $notification->refresh()->load(['user.role', 'creator.role', 'school']);
    }

    public function markAllRead(User $actor): int
    {
        return Notification::query()
            ->where('user_id', $actor->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }

    public function assertAccessible(Notification $notification, User $actor): void
    {
        if ($actor->role?->name === 'super_admin') {
            return;
        }

        $this->tenantService->assertCanAccessSchoolId($actor, $notification->school_id !== null ? (int) $notification->school_id : null);
    }
}
