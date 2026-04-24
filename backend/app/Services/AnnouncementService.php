<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementView;
use App\Models\School;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AnnouncementService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly TenantService $tenantService,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function listFor(User $actor): Collection
    {
        return $this->visibleQueryFor($actor)
            ->latest('created_at')
            ->get();
    }

    public function findFor(User $actor, Announcement $announcement): Announcement
    {
        $record = $this->visibleQueryFor($actor)
            ->whereKey($announcement->id)
            ->first();

        if ($record === null) {
            throw new AuthorizationException('لا تملك صلاحية الوصول إلى هذا الإعلان.');
        }

        $this->recordView($record, $actor);

        $record = $record->refresh()->load(['school', 'creator.role']);

        if ($this->canViewAnnouncementViews($actor)) {
            $record->load('views.viewer.role');
        }

        return $record;
    }

    public function create(array $data, User $actor): Announcement
    {
        $payload = $this->normalizePayload($data, $actor);

        $announcement = Announcement::query()->create([
            'uuid' => (string) Str::uuid(),
            'created_by_user_id' => $actor->id,
            ...$payload,
            'published_at' => ($payload['status'] ?? 'active') === 'active' ? now() : null,
        ]);

        if (($announcement->status ?? 'active') === 'active') {
            $this->dispatchAnnouncementNotifications($announcement->load(['school', 'creator.role']));
        }

        return $announcement->load(['school', 'creator.role']);
    }

    public function update(Announcement $announcement, array $data, User $actor): Announcement
    {
        $payload = $this->normalizePayload($data, $actor, true, $announcement);
        $announcement->update([
            ...$payload,
            'published_at' => ($payload['status'] ?? $announcement->status) === 'active'
                ? ($announcement->published_at ?? now())
                : null,
        ]);

        return $announcement->refresh()->load(['school', 'creator.role']);
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }

    private function normalizePayload(
        array $data,
        User $actor,
        bool $partial = false,
        ?Announcement $announcement = null,
    ): array {
        $isSuperAdmin = $actor->role?->name === 'super_admin';
        $isAllSchools = (bool) ($data['is_all_schools'] ?? ($announcement?->is_all_schools ?? false));
        $schoolId = array_key_exists('school_id', $data)
            ? ($data['school_id'] !== null ? (int) $data['school_id'] : null)
            : ($announcement?->school_id !== null ? (int) $announcement->school_id : null);

        if ($isSuperAdmin) {
            if (! $isAllSchools && $schoolId === null) {
                throw ValidationException::withMessages([
                    'school_id' => ['يجب تحديد المدرسة المستهدفة أو اختيار جميع المدارس.'],
                ]);
            }

            if ($isAllSchools) {
                $schoolId = null;
            }
        } else {
            $isAllSchools = false;
            $schoolId = (int) $actor->school_id;

            if ($schoolId <= 0 || ! School::query()->whereKey($schoolId)->exists()) {
                throw ValidationException::withMessages([
                    'school_id' => ['مدير المدرسة يجب أن يكون مرتبطًا بمدرسة فعالة لإنشاء إعلان.'],
                ]);
            }
        }

        $payload = [];

        if (! $partial || array_key_exists('title', $data)) {
            $payload['title'] = $data['title'];
        }

        if (! $partial || array_key_exists('body', $data)) {
            $payload['body'] = $data['body'];
        }

        if (! $partial || array_key_exists('target_audience', $data)) {
            $payload['target_audience'] = $data['target_audience'];
        }

        if (! $partial || array_key_exists('status', $data)) {
            $payload['status'] = $data['status'] ?? 'active';
        }

        $payload['is_all_schools'] = $isAllSchools;
        $payload['school_id'] = $schoolId;

        return $payload;
    }

    private function dispatchAnnouncementNotifications(Announcement $announcement): void
    {
        $users = User::query()
            ->with('role')
            ->where('status', 'active')
            ->when(! $announcement->is_all_schools, function (Builder $query) use ($announcement): void {
                $query->where(function (Builder $scopeQuery) use ($announcement): void {
                    $scopeQuery
                        ->where('school_id', $announcement->school_id)
                        ->orWhereHas('schoolAssignments', fn (Builder $assignmentQuery) => $assignmentQuery->where('school_id', $announcement->school_id));
                });
            })
            ->get()
            ->filter(function (User $user) use ($announcement): bool {
                if ($announcement->target_audience === 'general') {
                    return true;
                }

                return $user->role?->name === $announcement->target_audience;
            })
            ->values();

        foreach ($users as $user) {
            $this->notificationService->send([
                'school_id' => $announcement->school_id,
                'user_id' => $user->id,
                'created_by_user_id' => $announcement->created_by_user_id,
                'type' => 'announcement.published',
                'title' => $announcement->title,
                'body' => $announcement->body,
                'data' => [
                    'entity_type' => 'announcement',
                    'entity_id' => $announcement->id,
                    'school_name' => $announcement->school?->name_ar,
                    'action_url' => '/app/notifications',
                    'action_label' => 'عرض الإشعار',
                ],
            ]);
        }
    }

    private function visibleQueryFor(User $actor): Builder
    {
        $roleName = $actor->role?->name;
        $accessibleSchoolIds = $this->tenantService->accessibleSchoolIds($actor);
        $activeSchoolId = $this->tenantContext->schoolId();

        return Announcement::query()
            ->with(['school', 'creator.role'])
            ->when($roleName !== 'super_admin', function (Builder $query) use (
                $accessibleSchoolIds,
                $activeSchoolId,
            ): void {
                $schoolIds = $activeSchoolId !== null ? [$activeSchoolId] : $accessibleSchoolIds;

                $query->where(function (Builder $scopeQuery) use ($schoolIds): void {
                    if ($schoolIds !== []) {
                        $scopeQuery->whereIn('school_id', $schoolIds);
                    }

                    $scopeQuery->orWhere('is_all_schools', true);
                });
            })
            ->when(
                in_array($roleName, ['teacher', 'principal', 'parent', 'supervisor'], true),
                fn (Builder $query) => $query->whereIn('target_audience', ['general', $roleName])
            );
    }

    private function recordView(Announcement $announcement, User $actor): void
    {
        AnnouncementView::query()->create([
            'announcement_id' => $announcement->id,
            'viewer_user_id' => $actor->id,
            'viewer_role' => $actor->role?->name,
            'viewed_at' => now(),
        ]);
    }

    private function canViewAnnouncementViews(User $actor): bool
    {
        return in_array($actor->role?->name, ['super_admin', 'admin'], true);
    }
}
