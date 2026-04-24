<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\IepPlanStatus;
use App\Models\IepPlan;
use App\Models\IepPlanApproval;
use App\Models\UserSchoolAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class IepWorkflowService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function submit(IepPlan $iepPlan, User $actor, ?string $notes = null): IepPlan
    {
        if ($iepPlan->goals()->count() === 0) {
            throw ValidationException::withMessages([
                'goals' => ['يجب إضافة هدف واحد على الأقل قبل إرسال الخطة للمراجعة.'],
            ]);
        }

        return $this->transition(
            $iepPlan,
            $actor,
            IepPlanStatus::PENDING_PRINCIPAL_REVIEW,
            [
                'submitted_at' => now(),
                'rejection_reason' => null,
                'rejected_at' => null,
                'principal_user_id' => $iepPlan->principal_user_id ?: $iepPlan->school?->principal_user_id,
            ],
            $notes,
            function (IepPlan $plan) use ($actor): void {
                if ($plan->principal_user_id === null) {
                    return;
                }

                $this->notificationService->send([
                    'school_id' => $plan->school_id,
                    'user_id' => $plan->principal_user_id,
                    'created_by_user_id' => $actor->id,
                    'type' => 'iep.pending_principal_review',
                    'title' => 'خطة فردية بانتظار اعتماد المدير',
                    'body' => sprintf(
                        'الخطة %s للمدرسة %s بانتظار اعتماد المدير.',
                        $plan->title,
                        $plan->school?->name_ar ?? 'غير محددة',
                    ),
                    'data' => $this->buildIepNotificationData($plan, $actor),
                ]);
            },
        );
    }

    public function principalApprove(IepPlan $iepPlan, User $actor, ?string $notes = null): IepPlan
    {
        return $this->transition(
            $iepPlan,
            $actor,
            IepPlanStatus::APPROVED,
            [
                'principal_user_id' => $actor->id,
                'approved_at' => now(),
                'rejection_reason' => null,
                'rejected_at' => null,
            ],
            $notes,
            function (IepPlan $plan) use ($actor): void {
                $teacherId = $plan->teacher_user_id;

                if ($teacherId !== null) {
                    $this->notificationService->send([
                        'school_id' => $plan->school_id,
                        'user_id' => $teacherId,
                        'created_by_user_id' => $actor->id,
                        'type' => 'iep.approved',
                        'title' => 'تم اعتماد الخطة الفردية',
                        'body' => sprintf(
                            'تم اعتماد الخطة %s من مدير المدرسة %s.',
                            $plan->title,
                            $plan->school?->name_ar ?? 'غير محددة',
                        ),
                        'data' => $this->buildIepNotificationData($plan, $actor),
                    ]);
                }

                $supervisorIds = UserSchoolAssignment::query()
                    ->where('school_id', $plan->school_id)
                    ->where('assignment_type', 'supervising')
                    ->pluck('user_id')
                    ->map(static fn (mixed $id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                foreach ($supervisorIds as $supervisorId) {
                    $this->notificationService->send([
                        'school_id' => $plan->school_id,
                        'user_id' => $supervisorId,
                        'created_by_user_id' => $actor->id,
                        'type' => 'iep.review_notice',
                        'title' => 'خطة فردية معتمدة للاطلاع',
                        'body' => sprintf(
                            'الخطة %s في مدرسة %s متاحة الآن للاطلاع.',
                            $plan->title,
                            $plan->school?->name_ar ?? 'غير محددة',
                        ),
                        'data' => $this->buildIepNotificationData($plan, $actor),
                    ]);
                }
            },
        );
    }

    public function supervisorApprove(IepPlan $iepPlan, User $actor, ?string $notes = null): IepPlan
    {
        return $this->transition(
            $iepPlan,
            $actor,
            IepPlanStatus::APPROVED,
            [
                'supervisor_user_id' => $actor->id,
                'approved_at' => now(),
                'rejection_reason' => null,
                'rejected_at' => null,
            ],
            $notes,
        );
    }

    public function acknowledge(IepPlan $iepPlan, User $actor, ?string $notes = null): IepPlan
    {
        return DB::transaction(function () use ($iepPlan, $actor, $notes): IepPlan {
            $existingApproval = IepPlanApproval::query()
                ->where('iep_plan_id', $iepPlan->id)
                ->where('action_by_user_id', $actor->id)
                ->where('action_role', 'parent')
                ->first();

            if ($existingApproval === null) {
                IepPlanApproval::query()->create([
                    'iep_plan_id' => $iepPlan->id,
                    'school_id' => $iepPlan->school_id,
                    'action_by_user_id' => $actor->id,
                    'action_role' => 'parent',
                    'from_status' => $iepPlan->status,
                    'to_status' => $iepPlan->status,
                    'notes' => $notes,
                    'created_at' => now(),
                ]);
            }

            return $iepPlan->refresh()->load(['approvals.actor', 'school', 'teacher', 'student']);
        });
    }

    public function reject(IepPlan $iepPlan, User $actor, string $reason, ?string $notes = null): IepPlan
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages([
                'reason' => ['سبب الرفض مطلوب.'],
            ]);
        }

        return $this->transition(
            $iepPlan,
            $actor,
            IepPlanStatus::REJECTED,
            [
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ],
            $notes,
        );
    }

    public function reopen(IepPlan $iepPlan, User $actor, ?string $notes = null): IepPlan
    {
        return $this->transition(
            $iepPlan,
            $actor,
            IepPlanStatus::DRAFT,
            [
                'rejected_at' => null,
                'rejection_reason' => null,
            ],
            $notes,
        );
    }

    private function transition(
        IepPlan $iepPlan,
        User $actor,
        string $targetStatus,
        array $extraUpdates = [],
        ?string $notes = null,
        ?callable $afterTransition = null,
    ): IepPlan {
        $allowedTransitions = config('workflows.iep.' . $iepPlan->status, []);

        if (! in_array($targetStatus, $allowedTransitions, true)) {
            throw ValidationException::withMessages([
                'status' => ['الانتقال المطلوب غير مسموح من الحالة الحالية.'],
            ]);
        }

        return DB::transaction(function () use ($iepPlan, $actor, $targetStatus, $extraUpdates, $notes, $afterTransition): IepPlan {
            $fromStatus = $iepPlan->status;

            $iepPlan->update([
                'status' => $targetStatus,
                ...$extraUpdates,
            ]);

            IepPlanApproval::query()->create([
                'iep_plan_id' => $iepPlan->id,
                'school_id' => $iepPlan->school_id,
                'action_by_user_id' => $actor->id,
                'action_role' => $actor->role?->name ?? 'unknown',
                'from_status' => $fromStatus,
                'to_status' => $targetStatus,
                'notes' => $notes,
                'created_at' => now(),
            ]);

            $plan = $iepPlan->refresh()->load(['approvals.actor', 'school', 'teacher']);

            if ($afterTransition !== null) {
                $afterTransition($plan);
            }

            return $plan;
        });
    }

    private function buildIepNotificationData(IepPlan $plan, User $actor): array
    {
        return [
            'entity_type' => 'iep_plan',
            'entity_id' => $plan->id,
            'school_name' => $plan->school?->name_ar,
            'teacher_name' => $plan->teacher?->full_name,
            'actor_name' => $actor->full_name,
            'action_url' => '/app/iep-plans/' . $plan->id,
            'action_label' => 'عرض الخطة',
        ];
    }
}
