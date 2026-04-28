<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleName;
use App\Models\EducationProgram;
use App\Models\School;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchoolService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $perPage = max(5, min($perPage, 100));
        $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));
        $actor = auth()->user();

        return School::query()
            ->with($this->schoolRelations())
            ->withCount('students')
            ->withCount([
                'users as teachers_count' => fn (Builder $query) => $query->whereHas(
                    'role',
                    fn (Builder $roleQuery) => $roleQuery->where('name', RoleName::TEACHER),
                ),
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('ministry_code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('program_type', 'like', "%{$search}%")
                        ->orWhere('stage', 'like', "%{$search}%");
                });
            })
            ->when($filters['name'] ?? null, fn ($query, $value) => $query->where('name_ar', 'like', '%' . $value . '%'))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['program_type'] ?? null, fn ($query, $value) => $query->where('program_type', 'like', '%' . $value . '%'))
            ->when($filters['stage'] ?? null, fn ($query, $value) => $query->where('stage', $value))
            ->when($filters['gender'] ?? null, fn ($query, $value) => $query->where('gender', $value))
            ->when($actor !== null, fn (Builder $query) => $this->tenantService->scopeAccessibleSchools($query, $actor, 'id'))
            ->orderBy('name_ar')
            ->paginate($perPage);
    }

    public function create(array $data): School
    {
        $data['uuid'] = (string) Str::uuid();
        $payload = $this->mapPayload($data);
        $payload['status'] = $payload['status'] ?? 'active';
        $payload['gender'] = $payload['gender'] ?? 'غير محدد';
        $payload['city'] = $payload['city'] ?? 'غير محدد';
        $payload['region'] = $payload['region'] ?? $payload['city'];

        $school = School::query()->create($payload);
        $school->forceFill([
            'school_code' => $this->generateSchoolCode((int) $school->id, (string) $school->stage),
            'slug' => $this->generateSchoolSlug((int) $school->id, (string) $school->stage),
            'ministry_code' => $school->ministry_code ?: $this->generateSchoolCode((int) $school->id, (string) $school->stage),
        ])->save();
        $this->syncSupervisorAssignment($school, $school->supervisor_id);
        $this->syncEducationPrograms($school, $data);

        return $this->load($school);
    }

    public function update(School $school, array $data): School
    {
        $school->update($this->mapPayload($data));
        $this->syncSupervisorAssignment($school, $school->supervisor_id);
        $this->syncEducationPrograms($school, $data);

        return $this->load($school->refresh());
    }

    public function changeStatus(School $school, string $status): School
    {
        $school->update(['status' => $status]);

        return $this->load($school->refresh());
    }

    public function deactivate(School $school): School
    {
        $school->update(['status' => 'inactive']);

        return $this->load($school->refresh());
    }

    public function assignPrincipal(School $school, int $principalUserId): School
    {
        $school->update([
            'principal_id' => $principalUserId,
            'principal_user_id' => $principalUserId,
        ]);

        return $this->load($school->refresh());
    }

    public function assignSupervisor(School $school, int $supervisorUserId): School
    {
        $school->update(['supervisor_id' => $supervisorUserId]);
        $this->syncSupervisorAssignment($school, $supervisorUserId);

        return $this->load($school->refresh());
    }

    public function stats(School $school): array
    {
        return [
            'students_count' => DB::table('students')->where('school_id', $school->id)->count(),
            'teachers_count' => DB::table('users')->where('school_id', $school->id)->whereIn('role_id', function ($query) {
                $query->select('id')->from('roles')->where('name', 'teacher');
            })->count(),
            'iep_plans_count' => DB::table('iep_plans')->where('school_id', $school->id)->count(),
        ];
    }

    public function load(School $school): School
    {
        return $school->load([
            ...$this->schoolRelations(),
        ])->loadCount([
            'students',
            'users as teachers_count' => fn (Builder $query) => $query->whereHas(
                'role',
                fn (Builder $roleQuery) => $roleQuery->where('name', RoleName::TEACHER),
            ),
        ]);
    }

    public function assertAccessible(School $school, \App\Models\User $actor): void
    {
        $this->tenantService->assertCanAccessSchoolId($actor, (int) $school->id);
    }

    private function mapPayload(array $data): array
    {
        $payload = [];

        if (array_key_exists('uuid', $data)) {
            $payload['uuid'] = $data['uuid'];
        }

        if (array_key_exists('name', $data)) {
            $payload['name_ar'] = $data['name'];
        }

        if (array_key_exists('stage', $data)) {
            $payload['stage'] = $data['stage'];
        }

        if (array_key_exists('program_types', $data)) {
            $payload['program_type'] = $this->normalizeProgramNames($data['program_types'])->implode('، ');
        } elseif (array_key_exists('program_type', $data)) {
            $payload['program_type'] = $this->normalizeProgramNames($data['program_type'])->implode('، ');
        }

        if (array_key_exists('gender', $data)) {
            $payload['gender'] = $data['gender'];
        }

        if (array_key_exists('city', $data)) {
            $payload['city'] = $data['city'];
            $payload['region'] = $data['city'];
        }

        if (array_key_exists('address', $data)) {
            $payload['address'] = $data['address'];
        }

        if (array_key_exists('location_lat', $data)) {
            $payload['location_lat'] = $data['location_lat'];
            $payload['latitude'] = $data['location_lat'];
        }

        if (array_key_exists('location_lng', $data)) {
            $payload['location_lng'] = $data['location_lng'];
            $payload['longitude'] = $data['location_lng'];
        }

        if (array_key_exists('principal_id', $data)) {
            $payload['principal_id'] = $data['principal_id'];
            $payload['principal_user_id'] = $data['principal_id'];
        }

        if (array_key_exists('supervisor_id', $data)) {
            $payload['supervisor_id'] = $data['supervisor_id'];
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $data['status'];
        }

        return $payload;
    }

    private function syncEducationPrograms(School $school, array $data): void
    {
        if (! $this->hasSchoolProgramPivot()) {
            return;
        }

        $programNames = null;

        if (array_key_exists('program_types', $data)) {
            $programNames = $this->normalizeProgramNames($data['program_types']);
        } elseif (array_key_exists('program_type', $data)) {
            $programNames = $this->normalizeProgramNames($data['program_type']);
        }

        if ($programNames === null) {
            return;
        }

        $programIds = EducationProgram::query()
            ->whereIn('name_ar', $programNames->all())
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $school->educationPrograms()->sync($programIds);
    }

    private function normalizeProgramNames(mixed $programNames): \Illuminate\Support\Collection
    {
        if (is_string($programNames)) {
            $programNames = preg_split('/،|,/', $programNames) ?: [];
        }

        if (! is_array($programNames)) {
            return collect();
        }

        return collect($programNames)
            ->map(static fn (mixed $programName): string => trim((string) $programName))
            ->filter()
            ->unique()
            ->values();
    }

    private function syncSupervisorAssignment(School $school, ?int $supervisorUserId): void
    {
        DB::table('user_school_assignments')
            ->where('school_id', $school->id)
            ->where('assignment_type', 'supervising')
            ->delete();

        if ($supervisorUserId === null) {
            return;
        }

        DB::table('user_school_assignments')->insert([
            'user_id' => $supervisorUserId,
            'school_id' => $school->id,
            'assignment_type' => 'supervising',
            'created_at' => now(),
        ]);
    }

    private function generateSchoolCode(int $schoolId, string $stage): string
    {
        $stageKey = match (trim($stage)) {
            'ثانوي' => 'S',
            'متوسط' => 'I',
            default => 'P',
        };

        return sprintf('JED-%s-%05d', $stageKey, $schoolId);
    }

    private function generateSchoolSlug(int $schoolId, string $stage): string
    {
        return Str::lower($this->generateSchoolCode($schoolId, $stage));
    }

    private function schoolRelations(): array
    {
        $relations = [
            'principal:id,full_name,email',
            'supervisor:id,full_name,email',
        ];

        if ($this->hasSchoolProgramPivot()) {
            $relations[] = 'educationPrograms:id,code,name_ar';
        }

        return $relations;
    }

    private function hasSchoolProgramPivot(): bool
    {
        static $exists = null;

        if ($exists === null) {
            $exists = Schema::hasTable('education_program_school');
        }

        return $exists;
    }
}
