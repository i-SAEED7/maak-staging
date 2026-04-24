<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReportService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function schoolSummary(School $school, User $actor): array
    {
        $this->assertCanAccessSchool($actor, (int) $school->id);

        $schoolUsers = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.school_id', $school->id)
            ->selectRaw('COALESCE(roles.name, ?) as role_name, COUNT(*) as total', ['unknown'])
            ->groupBy('roles.name')
            ->pluck('total', 'role_name')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        $studentByStatus = DB::table('students')
            ->where('school_id', $school->id)
            ->selectRaw('COALESCE(enrollment_status, ?) as label, COUNT(*) as total', ['unknown'])
            ->groupBy('enrollment_status')
            ->pluck('total', 'label')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        $studentByGender = DB::table('students')
            ->where('school_id', $school->id)
            ->selectRaw('COALESCE(gender, ?) as label, COUNT(*) as total', ['unknown'])
            ->groupBy('gender')
            ->pluck('total', 'label')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        $studentByGrade = DB::table('students')
            ->where('school_id', $school->id)
            ->selectRaw('COALESCE(grade_level, ?) as label, COUNT(*) as total', ['غير محدد'])
            ->groupBy('grade_level')
            ->pluck('total', 'label')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        $iepByStatus = DB::table('iep_plans')
            ->where('school_id', $school->id)
            ->selectRaw('COALESCE(status, ?) as label, COUNT(*) as total', ['unknown'])
            ->groupBy('status')
            ->pluck('total', 'label')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        $principal = DB::table('users')
            ->where('id', $school->principal_user_id)
            ->first(['id', 'full_name', 'email']);

        $supervisors = DB::table('user_school_assignments')
            ->join('users', 'users.id', '=', 'user_school_assignments.user_id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('user_school_assignments.school_id', $school->id)
            ->where('roles.name', 'supervisor')
            ->orderBy('users.full_name')
            ->get(['users.id', 'users.full_name', 'users.email'])
            ->map(static fn (object $supervisor): array => [
                'id' => $supervisor->id,
                'full_name' => $supervisor->full_name,
                'email' => $supervisor->email,
            ])
            ->all();

        return [
            'school' => [
                'id' => $school->id,
                'name_ar' => $school->name_ar,
                'official_code' => $school->ministry_code,
                'stage' => $school->stage,
                'program_type' => $school->program_type,
                'gender' => $school->gender,
                'region' => $school->region,
                'city' => $school->city,
                'status' => $school->status,
            ],
            'overview' => [
                'students_count' => (int) DB::table('students')->where('school_id', $school->id)->count(),
                'teachers_count' => (int) ($schoolUsers['teacher'] ?? 0),
                'users_count' => (int) DB::table('users')->where('school_id', $school->id)->count(),
                'iep_plans_count' => (int) DB::table('iep_plans')->where('school_id', $school->id)->count(),
                'approved_iep_plans_count' => (int) DB::table('iep_plans')->where('school_id', $school->id)->where('status', 'approved')->count(),
                'messages_count' => (int) DB::table('messages')->where('school_id', $school->id)->count(),
                'notifications_count' => (int) DB::table('notifications')->where('school_id', $school->id)->count(),
                'unread_notifications_count' => (int) DB::table('notifications')->where('school_id', $school->id)->whereNull('read_at')->count(),
                'files_count' => (int) DB::table('files')->where('school_id', $school->id)->whereNull('deleted_at')->count(),
            ],
            'leadership' => [
                'principal' => $principal ? [
                    'id' => $principal->id,
                    'full_name' => $principal->full_name,
                    'email' => $principal->email,
                ] : null,
                'supervisors' => $supervisors,
            ],
            'breakdowns' => [
                'users_by_role' => $schoolUsers,
                'students_by_status' => $studentByStatus,
                'students_by_gender' => $studentByGender,
                'students_by_grade' => $studentByGrade,
                'iep_by_status' => $iepByStatus,
            ],
        ];
    }

    public function studentSummary(Student $student, User $actor): array
    {
        $this->assertCanAccessStudent($actor, $student);

        $student = Student::withoutGlobalScopes()
            ->with(['school', 'academicYear', 'educationProgram', 'disabilityCategory', 'primaryTeacher', 'guardians.parent', 'iepPlans'])
            ->findOrFail($student->id);

        $iepCounts = $student->iepPlans
            ->groupBy('status')
            ->map(static fn (Collection $plans): int => $plans->count())
            ->all();

        $latestPlan = $student->iepPlans
            ->sortByDesc('updated_at')
            ->first();

        return [
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'student_number' => $student->student_number,
                'gender' => $student->gender,
                'grade_level' => $student->grade_level,
                'classroom' => $student->classroom,
                'enrollment_status' => $student->enrollment_status,
            ],
            'school' => [
                'id' => $student->school?->id,
                'name_ar' => $student->school?->name_ar,
            ],
            'education' => [
                'academic_year' => $student->academicYear?->name_ar,
                'program' => $student->educationProgram?->name_ar,
                'disability_category' => $student->disabilityCategory?->name_ar,
                'primary_teacher' => $student->primaryTeacher?->full_name,
            ],
            'guardians' => $student->guardians->map(static fn ($guardian): array => [
                'id' => $guardian->id,
                'parent_user_id' => $guardian->parent_user_id,
                'parent_name' => $guardian->parent?->full_name,
                'relationship' => $guardian->relationship,
                'is_primary' => (bool) $guardian->is_primary,
            ])->values()->all(),
            'iep' => [
                'plans_count' => $student->iepPlans->count(),
                'counts_by_status' => $iepCounts,
                'latest_plan' => $latestPlan ? [
                    'id' => $latestPlan->id,
                    'title' => $latestPlan->title,
                    'status' => $latestPlan->status,
                    'current_version_number' => $latestPlan->current_version_number,
                    'updated_at' => $latestPlan->updated_at?->toAtomString(),
                ] : null,
            ],
            'activity' => [
                'portfolio_items_count' => (int) DB::table('portfolio_items')
                    ->join('portfolios', 'portfolios.id', '=', 'portfolio_items.portfolio_id')
                    ->where('portfolios.student_id', $student->id)
                    ->count(),
                'student_reports_count' => (int) DB::table('student_reports')->where('student_id', $student->id)->count(),
            ],
        ];
    }

    public function comparison(User $actor, array $filters = []): array
    {
        $schoolIds = $this->resolveSchoolIdsForReport($actor, $filters['school_ids'] ?? null);

        return School::query()
            ->whereIn('id', $schoolIds)
            ->orderBy('name_ar')
            ->get()
            ->map(function (School $school): array {
                $studentCount = (int) DB::table('students')->where('school_id', $school->id)->count();
                $teacherCount = (int) DB::table('users')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('users.school_id', $school->id)
                    ->where('roles.name', 'teacher')
                    ->count();

                $iepCount = (int) DB::table('iep_plans')->where('school_id', $school->id)->count();

                return [
                    'school_id' => $school->id,
                    'official_code' => $school->ministry_code,
                    'school_name' => $school->name_ar,
                    'stage' => $school->stage,
                    'program_type' => $school->program_type,
                    'status' => $school->status,
                    'students_count' => $studentCount,
                    'active_students_count' => (int) DB::table('students')->where('school_id', $school->id)->where('enrollment_status', 'active')->count(),
                    'teachers_count' => $teacherCount,
                    'iep_plans_count' => $iepCount,
                    'approved_iep_plans_count' => (int) DB::table('iep_plans')->where('school_id', $school->id)->where('status', 'approved')->count(),
                    'messages_count' => (int) DB::table('messages')->where('school_id', $school->id)->count(),
                    'notifications_count' => (int) DB::table('notifications')->where('school_id', $school->id)->count(),
                    'files_count' => (int) DB::table('files')->where('school_id', $school->id)->whereNull('deleted_at')->count(),
                    'student_teacher_ratio' => $teacherCount > 0 ? round($studentCount / $teacherCount, 2) : null,
                ];
            })
            ->values()
            ->all();
    }

    public function pivot(User $actor, array $filters = []): array
    {
        $dimension = (string) ($filters['dimension'] ?? 'grade_level');
        $schoolIds = $this->resolveSchoolIdsForReport($actor, $filters['school_ids'] ?? ($filters['school_id'] ?? null));

        $rows = match ($dimension) {
            'gender' => DB::table('students')
                ->whereIn('school_id', $schoolIds)
                ->selectRaw('COALESCE(gender, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('gender')
                ->orderByDesc('value')
                ->get(),
            'disability_category' => DB::table('students')
                ->leftJoin('disability_categories', 'disability_categories.id', '=', 'students.disability_category_id')
                ->whereIn('students.school_id', $schoolIds)
                ->selectRaw('COALESCE(disability_categories.name_ar, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('disability_categories.name_ar')
                ->orderByDesc('value')
                ->get(),
            'education_program' => DB::table('students')
                ->leftJoin('education_programs', 'education_programs.id', '=', 'students.education_program_id')
                ->whereIn('students.school_id', $schoolIds)
                ->selectRaw('COALESCE(education_programs.name_ar, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('education_programs.name_ar')
                ->orderByDesc('value')
                ->get(),
            'iep_status' => DB::table('iep_plans')
                ->whereIn('school_id', $schoolIds)
                ->selectRaw('COALESCE(status, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('status')
                ->orderByDesc('value')
                ->get(),
            'teacher' => DB::table('students')
                ->leftJoin('users', 'users.id', '=', 'students.primary_teacher_user_id')
                ->whereIn('students.school_id', $schoolIds)
                ->selectRaw('COALESCE(users.full_name, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('users.full_name')
                ->orderByDesc('value')
                ->get(),
            default => DB::table('students')
                ->whereIn('school_id', $schoolIds)
                ->selectRaw('COALESCE(grade_level, ?) as label, COUNT(*) as value', ['غير محدد'])
                ->groupBy('grade_level')
                ->orderByDesc('value')
                ->get(),
        };

        return [
            'dimension' => $dimension,
            'school_ids' => $schoolIds,
            'rows' => collect($rows)->map(static fn (object $row): array => [
                'label' => $row->label,
                'value' => (int) $row->value,
            ])->values()->all(),
        ];
    }

    public function export(string $format, User $actor, array $filters = []): array
    {
        $type = (string) ($filters['type'] ?? 'comparison');

        $preview = match ($type) {
            'school_summary' => isset($filters['school_id'])
                ? $this->schoolSummary(School::query()->findOrFail((int) $filters['school_id']), $actor)
                : throw ValidationException::withMessages(['school_id' => ['school_id مطلوب لهذا النوع من التقارير.']]),
            'student_summary' => isset($filters['student_id'])
                ? $this->studentSummary(Student::withoutGlobalScopes()->findOrFail((int) $filters['student_id']), $actor)
                : throw ValidationException::withMessages(['student_id' => ['student_id مطلوب لهذا النوع من التقارير.']]),
            'pivot' => $this->pivot($actor, $filters),
            default => $this->comparison($actor, $filters),
        };

        return [
            'format' => $format,
            'type' => $type,
            'generated_at' => now()->toAtomString(),
            'download_available' => false,
            'queued' => false,
            'message' => 'تم تجهيز معاينة التصدير. يمكن ربطه لاحقًا بوظائف PDF/Excel الفعلية.',
            'preview' => $preview,
        ];
    }

    private function resolveSchoolIdsForReport(User $actor, mixed $requestedSchoolIds): array
    {
        $allowedSchoolIds = $this->accessibleSchoolIdsForReports($actor);

        if ($allowedSchoolIds === []) {
            throw new AuthorizationException('لا توجد مدارس متاحة لهذا المستخدم ضمن نطاق التقرير.');
        }

        if ($requestedSchoolIds === null || $requestedSchoolIds === '') {
            return $allowedSchoolIds;
        }

        $normalized = collect(is_array($requestedSchoolIds) ? $requestedSchoolIds : [$requestedSchoolIds])
            ->flatMap(static fn (mixed $value) => is_string($value) && str_contains($value, ',')
                ? explode(',', $value)
                : [$value])
            ->map(static fn (mixed $value): int => (int) $value)
            ->filter(static fn (int $value): bool => $value > 0)
            ->unique()
            ->values()
            ->all();

        foreach ($normalized as $schoolId) {
            if (! in_array($schoolId, $allowedSchoolIds, true)) {
                throw new AuthorizationException('إحدى المدارس المطلوبة غير متاحة لهذا المستخدم.');
            }
        }

        return $normalized;
    }

    private function accessibleSchoolIdsForReports(User $actor): array
    {
        if (in_array($actor->role?->name, ['super_admin', 'admin'], true)) {
            return School::query()->pluck('id')->map(static fn (mixed $id): int => (int) $id)->all();
        }

        return $this->tenantService->accessibleSchoolIds($actor);
    }

    private function assertCanAccessSchool(User $actor, int $schoolId): void
    {
        if (! in_array($schoolId, $this->accessibleSchoolIdsForReports($actor), true)) {
            throw new AuthorizationException('لا يمكن الوصول إلى هذه المدرسة ضمن التقرير المطلوب.');
        }
    }

    private function assertCanAccessStudent(User $actor, Student $student): void
    {
        if ($actor->role?->name === 'parent') {
            $isGuardian = DB::table('student_guardians')
                ->where('student_id', $student->id)
                ->where('parent_user_id', $actor->id)
                ->exists();

            if (! $isGuardian) {
                throw new AuthorizationException('لا يمكن الوصول إلى هذا الطالب ضمن التقرير المطلوب.');
            }

            return;
        }

        $this->assertCanAccessSchool($actor, (int) $student->school_id);
    }
}
