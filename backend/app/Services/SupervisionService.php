<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SupervisorVisit;
use App\Models\SupervisorVisitItem;
use App\Models\SupervisorVisitRecommendation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class SupervisionService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginateVisits(User $actor, array $filters = []): LengthAwarePaginator
    {
        $schoolIds = $this->tenantService->accessibleSchoolIds($actor);

        return SupervisorVisit::query()
            ->with(['school', 'supervisor', 'items', 'recommendations.owner'])
            ->when($schoolIds !== [], fn ($query) => $query->whereIn('school_id', $schoolIds))
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', $value))
            ->when($filters['visit_status'] ?? null, fn ($query, $value) => $query->where('visit_status', $value))
            ->orderByDesc('visit_date')
            ->paginate(15);
    }

    public function createVisit(array $data, User $actor): SupervisorVisit
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $data['school_id']);

        $visit = SupervisorVisit::query()->create([
            'uuid' => (string) Str::uuid(),
            'school_id' => $data['school_id'],
            'supervisor_user_id' => $actor->id,
            'template_id' => $data['template_id'] ?? null,
            'visit_date' => $data['visit_date'],
            'visit_status' => 'scheduled',
            'agenda' => $data['agenda'] ?? null,
            'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
        ]);

        return $this->loadVisit($visit);
    }

    public function updateVisit(SupervisorVisit $visit, array $data, User $actor): SupervisorVisit
    {
        $this->assertAccessible($visit, $actor);

        $visit->update($data);

        return $this->loadVisit($visit->refresh());
    }

    public function completeVisit(SupervisorVisit $visit, array $data, User $actor): SupervisorVisit
    {
        $this->assertAccessible($visit, $actor);

        return DB::transaction(function () use ($visit, $data): SupervisorVisit {
            SupervisorVisitItem::query()->where('visit_id', $visit->id)->delete();

            foreach ($data['items'] as $item) {
                SupervisorVisitItem::query()->create([
                    'visit_id' => $visit->id,
                    'school_id' => $visit->school_id,
                    'criterion_key' => $item['criterion_key'],
                    'criterion_label' => $item['criterion_label'],
                    'score' => $item['score'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            $scores = collect($data['items'])
                ->pluck('score')
                ->filter(static fn ($score) => $score !== null && $score !== '');

            $overallScore = $data['overall_score'] ?? ($scores->isNotEmpty() ? round((float) $scores->avg(), 2) : null);

            $visit->update([
                'visit_status' => 'completed',
                'summary' => $data['summary'] ?? null,
                'overall_score' => $overallScore,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
            ]);

            return $this->loadVisit($visit->refresh());
        });
    }

    public function addRecommendation(SupervisorVisit $visit, array $data, User $actor): SupervisorVisitRecommendation
    {
        $this->assertAccessible($visit, $actor);

        return SupervisorVisitRecommendation::query()->create([
            'visit_id' => $visit->id,
            'school_id' => $visit->school_id,
            'recommendation_text' => $data['recommendation_text'],
            'owner_user_id' => $data['owner_user_id'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => $data['status'] ?? 'open',
            'completed_at' => ($data['status'] ?? null) === 'completed' ? now() : null,
        ])->load('owner');
    }

    public function updateRecommendation(SupervisorVisitRecommendation $recommendation, array $data, User $actor): SupervisorVisitRecommendation
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $recommendation->school_id);

        $status = $data['status'] ?? $recommendation->status;

        $recommendation->update([
            ...$data,
            'completed_at' => $status === 'completed'
                ? ($recommendation->completed_at ?? now())
                : ($status === 'open' || $status === 'in_progress' ? null : $recommendation->completed_at),
        ]);

        return $recommendation->refresh()->load('owner');
    }

    public function assertAccessible(SupervisorVisit $visit, User $actor): void
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $visit->school_id);
    }

    public function loadVisit(SupervisorVisit $visit): SupervisorVisit
    {
        return $visit->load(['school', 'supervisor', 'items', 'recommendations.owner']);
    }
}
