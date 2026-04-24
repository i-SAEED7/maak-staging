<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\IepPlanStatus;
use App\Models\IepPlan;
use App\Models\IepPlanComment;
use App\Models\IepPlanGoal;
use App\Models\IepPlanVersion;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class IepPlanService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $actor = auth()->user();
        $tenantSchoolId = app(TenantContext::class)->schoolId();

        return IepPlan::query()
            ->with(['school', 'student.school', 'student.educationProgram', 'teacher', 'principal', 'supervisor', 'goals'])
            ->when(
                $actor !== null && $actor->role?->name === 'parent',
                fn ($query) => $query->with('approvals.actor'),
            )
            ->when(
                $actor !== null && $actor->role?->name === 'supervisor' && $tenantSchoolId === null,
                fn ($query) => $this->tenantService->scopeAccessibleSchools($query, $actor),
            )
            ->when(
                $actor !== null && $actor->role?->name === 'parent',
                fn ($query) => $query->whereHas('student.guardians', fn ($guardianQuery) => $guardianQuery->where('parent_user_id', $actor->id)),
            )
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['student_id'] ?? null, fn ($query, $value) => $query->where('student_id', $value))
            ->when($filters['teacher_user_id'] ?? null, fn ($query, $value) => $query->where('teacher_user_id', $value))
            ->when(
                $filters['education_program_id'] ?? null,
                fn ($query, $value) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('education_program_id', $value)),
            )
            ->when(
                $filters['grade_level'] ?? null,
                fn ($query, $value) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('grade_level', $value)),
            )
            ->when(
                $filters['classroom'] ?? null,
                fn ($query, $value) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('classroom', $value)),
            )
            ->when($filters['search'] ?? null, function ($query, $value) {
                $query->where(function ($builder) use ($value): void {
                    $builder
                        ->where('title', 'like', "%{$value}%")
                        ->orWhereHas('student', fn ($studentQuery) => $studentQuery->where('full_name', 'like', "%{$value}%"));
                });
            })
            ->latest('updated_at')
            ->paginate(15);
    }

    public function create(array $data, User $actor): IepPlan
    {
        $student = Student::withoutGlobalScopes()->findOrFail($data['student_id']);
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $student->school_id);

        return DB::transaction(function () use ($data, $actor, $student): IepPlan {
            $plan = IepPlan::query()->create([
                'uuid' => (string) Str::uuid(),
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'academic_year_id' => $data['academic_year_id'] ?? $student->academic_year_id,
                'teacher_user_id' => $student->primary_teacher_user_id ?? $actor->id,
                'principal_user_id' => $student->school?->principal_user_id,
                'current_version_number' => 1,
                'status' => IepPlanStatus::DRAFT,
                'title' => $data['title'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'summary' => $data['summary'] ?? null,
                'strengths' => $data['strengths'] ?? null,
                'needs' => $data['needs'] ?? null,
                'accommodations' => $data['accommodations'] ?? null,
            ]);

            $this->syncGoals($plan, $data['goals'] ?? []);
            $this->createVersion($plan->refresh(), $actor, 'الإصدار الأول عند إنشاء الخطة');

            return $this->loadPlan($plan->refresh());
        });
    }

    public function update(IepPlan $iepPlan, array $data, User $actor): IepPlan
    {
        $this->ensureEditable($iepPlan);
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $iepPlan->school_id);

        return DB::transaction(function () use ($iepPlan, $data, $actor): IepPlan {
            $payload = array_intersect_key($data, array_flip([
                'academic_year_id',
                'title',
                'start_date',
                'end_date',
                'summary',
                'strengths',
                'needs',
                'accommodations',
            ]));

            if ($payload !== []) {
                $payload['current_version_number'] = (int) $iepPlan->current_version_number + 1;
                $iepPlan->update($payload);
            }

            if (array_key_exists('goals', $data)) {
                if (! array_key_exists('current_version_number', $payload)) {
                    $iepPlan->update([
                        'current_version_number' => (int) $iepPlan->current_version_number + 1,
                    ]);
                }

                $this->syncGoals($iepPlan->refresh(), $data['goals'] ?? []);
            }

            $plan = $iepPlan->refresh();
            $this->createVersion($plan, $actor, 'تحديث محتوى الخطة');

            return $this->loadPlan($plan->refresh());
        });
    }

    public function addComment(IepPlan $iepPlan, array $data, User $actor): IepPlanComment
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $iepPlan->school_id);

        return IepPlanComment::query()->create([
            'iep_plan_id' => $iepPlan->id,
            'school_id' => $iepPlan->school_id,
            'author_user_id' => $actor->id,
            'target_section' => $data['target_section'] ?? null,
            'comment_text' => $data['comment_text'],
            'is_internal' => (bool) ($data['is_internal'] ?? false),
        ])->load('author');
    }

    public function versions(IepPlan $iepPlan): IepPlan
    {
        return $iepPlan->load(['versions.creator']);
    }

    public function delete(IepPlan $iepPlan, User $actor): void
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $iepPlan->school_id);
        $iepPlan->delete();
    }

    public function pdfPayload(IepPlan $iepPlan): array
    {
        return [
            'download_available' => $iepPlan->generated_pdf_file_id !== null,
            'generated_pdf_file_id' => $iepPlan->generated_pdf_file_id,
            'status' => $iepPlan->status,
            'message' => $iepPlan->generated_pdf_file_id !== null
                ? 'ملف PDF جاهز للربط لاحقًا مع وحدة الملفات.'
                : 'لم يتم توليد PDF بعد لهذه الخطة.',
        ];
    }

    public function assertAccessible(IepPlan $iepPlan, User $actor): void
    {
        if ($actor->role?->name === 'parent') {
            $isGuardian = StudentGuardian::query()
                ->where('student_id', $iepPlan->student_id)
                ->where('parent_user_id', $actor->id)
                ->exists();

            if (! $isGuardian) {
                throw ValidationException::withMessages([
                    'iep_plan_id' => ['الخطة غير متاحة لهذا المستخدم.'],
                ]);
            }

            return;
        }

        $this->tenantService->assertCanAccessSchoolId($actor, (int) $iepPlan->school_id);
    }

    public function loadPlan(IepPlan $iepPlan): IepPlan
    {
        return $iepPlan->load([
            'school',
            'student',
            'student.school',
            'student.educationProgram',
            'teacher',
            'principal',
            'supervisor',
            'goals',
            'comments.author',
            'versions.creator',
            'approvals.actor',
        ]);
    }

    private function ensureEditable(IepPlan $iepPlan): void
    {
        if (! in_array($iepPlan->status, [IepPlanStatus::DRAFT, IepPlanStatus::REJECTED], true)) {
            throw ValidationException::withMessages([
                'status' => ['لا يمكن تعديل الخطة في حالتها الحالية.'],
            ]);
        }
    }

    private function syncGoals(IepPlan $iepPlan, array $goals): void
    {
        IepPlanGoal::query()->where('iep_plan_id', $iepPlan->id)->delete();

        foreach (array_values($goals) as $index => $goal) {
            IepPlanGoal::query()->create([
                'iep_plan_id' => $iepPlan->id,
                'school_id' => $iepPlan->school_id,
                'domain' => $goal['domain'],
                'goal_text' => $goal['goal_text'],
                'measurement_method' => $goal['measurement_method'] ?? null,
                'baseline_value' => $goal['baseline_value'] ?? null,
                'target_value' => $goal['target_value'] ?? null,
                'due_date' => $goal['due_date'] ?? null,
                'sort_order' => $goal['sort_order'] ?? $index,
            ]);
        }
    }

    private function createVersion(IepPlan $iepPlan, User $actor, string $changeSummary): void
    {
        IepPlanVersion::query()->create([
            'iep_plan_id' => $iepPlan->id,
            'school_id' => $iepPlan->school_id,
            'version_number' => $iepPlan->current_version_number,
            'content_json' => [
                'title' => $iepPlan->title,
                'status' => $iepPlan->status,
                'student_id' => $iepPlan->student_id,
                'academic_year_id' => $iepPlan->academic_year_id,
                'start_date' => $iepPlan->start_date?->toDateString(),
                'end_date' => $iepPlan->end_date?->toDateString(),
                'summary' => $iepPlan->summary,
                'strengths' => $iepPlan->strengths,
                'needs' => $iepPlan->needs,
                'accommodations' => $iepPlan->accommodations,
                'goals' => $iepPlan->goals()->orderBy('sort_order')->get()->map(static fn ($goal): array => [
                    'domain' => $goal->domain,
                    'goal_text' => $goal->goal_text,
                    'measurement_method' => $goal->measurement_method,
                    'baseline_value' => $goal->baseline_value,
                    'target_value' => $goal->target_value,
                    'due_date' => $goal->due_date?->toDateString(),
                    'sort_order' => $goal->sort_order,
                ])->values()->all(),
            ],
            'change_summary' => $changeSummary,
            'created_by_user_id' => $actor->id,
            'created_at' => now(),
        ]);
    }
}
