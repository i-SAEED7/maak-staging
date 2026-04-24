<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Support\TenantContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class StudentService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $actor = auth()->user();
        $tenantSchoolId = app(TenantContext::class)->schoolId();

        return Student::query()
            ->with(['school', 'academicYear', 'educationProgram', 'disabilityCategory', 'primaryTeacher', 'guardians.parent'])
            ->when($filters['search'] ?? null, function ($query, $value) {
                $query->where(function ($studentQuery) use ($value): void {
                    $studentQuery
                        ->where('full_name', 'like', "%{$value}%")
                        ->orWhere('student_number', 'like', "%{$value}%");
                });
            })
            ->when(
                $actor !== null && $actor->role?->name === 'supervisor' && $tenantSchoolId === null,
                fn ($query) => $this->tenantService->scopeAccessibleSchools($query, $actor),
            )
            ->when(
                $actor !== null && $actor->role?->name === 'parent',
                fn ($query) => $query->whereHas('guardians', fn ($guardianQuery) => $guardianQuery->where('parent_user_id', $actor->id)),
            )
            ->when($filters['school_id'] ?? null, fn ($query, $value) => $query->where('school_id', $value))
            ->when($filters['enrollment_status'] ?? null, fn ($query, $value) => $query->where('enrollment_status', $value))
            ->when($filters['education_program_id'] ?? null, fn ($query, $value) => $query->where('education_program_id', $value))
            ->when($filters['primary_teacher_user_id'] ?? null, fn ($query, $value) => $query->where('primary_teacher_user_id', $value))
            ->orderBy('full_name')
            ->paginate(15);
    }

    public function create(array $data): Student
    {
        $schoolId = $data['school_id'] ?? app(\App\Support\TenantContext::class)->schoolId();
        $actor = auth()->user();

        if ($schoolId === null) {
            throw ValidationException::withMessages([
                'school_id' => ['يجب تحديد المدرسة للطالب الجديد.'],
            ]);
        }

        if ($actor !== null) {
            $this->tenantService->assertCanAccessSchoolId($actor, (int) $schoolId);
        }

        $primaryTeacherUserId = $data['primary_teacher_user_id']
            ?? (($actor?->role?->name === \App\Enums\RoleName::TEACHER) ? $actor->id : null);

        $student = Student::query()->create([
            ...$data,
            'uuid' => (string) Str::uuid(),
            'school_id' => $schoolId,
            'primary_teacher_user_id' => $primaryTeacherUserId,
            'full_name' => $this->buildFullName(
                $data['first_name'],
                $data['father_name'] ?? null,
                $data['grandfather_name'] ?? null,
                $data['family_name'],
            ),
            'student_number' => $data['student_number'] ?? null,
            'enrollment_status' => $data['enrollment_status'] ?? 'active',
        ]);

        if (! $student->student_number) {
            $student->forceFill([
                'student_number' => $this->generateStudentNumber($student),
            ])->save();
        }

        $this->syncPrimaryTeacherAssignment($student, $primaryTeacherUserId);

        return $this->loadStudent($student);
    }

    public function update(Student $student, array $data): Student
    {
        $actor = auth()->user();
        $targetSchoolId = isset($data['school_id']) && $data['school_id'] !== null
            ? (int) $data['school_id']
            : (int) $student->school_id;

        if ($actor !== null) {
            $this->tenantService->assertCanAccessSchoolId($actor, $targetSchoolId);
        }

        $payload = [
            ...$data,
            'full_name' => $this->buildFullName(
                $data['first_name'] ?? $student->first_name,
                $data['father_name'] ?? $student->father_name,
                $data['grandfather_name'] ?? $student->grandfather_name,
                $data['family_name'] ?? $student->family_name,
            ),
        ];

        $student->update($payload);

        if (array_key_exists('primary_teacher_user_id', $data)) {
            $this->syncPrimaryTeacherAssignment($student->refresh(), $data['primary_teacher_user_id']);
        }

        return $this->loadStudent($student->refresh());
    }

    public function archive(Student $student, ?string $reason = null): Student
    {
        $metadata = $student->metadata ?? [];
        $metadata['archive_reason'] = $reason;

        $student->update([
            'enrollment_status' => 'archived',
            'archived_at' => now(),
            'metadata' => $metadata,
        ]);

        return $this->loadStudent($student->refresh());
    }

    public function guardians(Student $student): Student
    {
        return $this->loadStudent($student);
    }

    public function assignGuardian(Student $student, array $data): StudentGuardian
    {
        return DB::transaction(function () use ($student, $data): StudentGuardian {
            if ((bool) ($data['is_primary'] ?? false)) {
                StudentGuardian::query()
                    ->where('student_id', $student->id)
                    ->update(['is_primary' => false]);
            }

            $guardian = StudentGuardian::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'parent_user_id' => $data['parent_user_id'],
                ],
                [
                    'relationship' => $data['relationship'],
                    'is_primary' => (bool) ($data['is_primary'] ?? false),
                    'can_view_reports' => (bool) ($data['can_view_reports'] ?? true),
                    'can_message_school' => (bool) ($data['can_message_school'] ?? true),
                ],
            );

            return $guardian->load('parent');
        });
    }

    public function assertAccessible(Student $student, \App\Models\User $user): void
    {
        if ($user->role?->name === 'parent') {
            $isGuardian = StudentGuardian::query()
                ->where('student_id', $student->id)
                ->where('parent_user_id', $user->id)
                ->exists();

            if (! $isGuardian) {
                throw ValidationException::withMessages([
                    'student_id' => ['الطالب غير متاح لهذا المستخدم.'],
                ]);
            }

            return;
        }

        $this->tenantService->assertCanAccessSchoolId($user, (int) $student->school_id);
    }

    private function syncPrimaryTeacherAssignment(Student $student, ?int $teacherUserId): void
    {
        DB::table('teacher_student_assignments')
            ->where('student_id', $student->id)
            ->where('assignment_role', 'primary')
            ->delete();

        if ($teacherUserId === null) {
            return;
        }

        DB::table('teacher_student_assignments')->insert([
            'school_id' => $student->school_id,
            'teacher_user_id' => $teacherUserId,
            'student_id' => $student->id,
            'assigned_by_user_id' => auth()->id(),
            'assignment_role' => 'primary',
            'starts_on' => now()->toDateString(),
            'ends_on' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buildFullName(string $firstName, ?string $fatherName, ?string $grandfatherName, string $familyName): string
    {
        return trim(implode(' ', array_filter([
            $firstName,
            $fatherName,
            $grandfatherName,
            $familyName,
        ])));
    }

    private function generateStudentNumber(Student $student): string
    {
        return sprintf('STD-%03d-%05d', (int) $student->school_id, (int) $student->id);
    }

    private function loadStudent(Student $student): Student
    {
        return $student->load([
            'school',
            'academicYear',
            'educationProgram',
            'disabilityCategory',
            'primaryTeacher',
            'guardians.parent',
        ]);
    }
}
