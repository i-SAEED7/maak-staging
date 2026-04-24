<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->string('school_code', 20)->nullable()->after('name_en')->unique();
            $table->string('slug', 40)->nullable()->after('school_code')->unique();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 60)->nullable()->after('full_name')->unique();
            $table->boolean('is_central')->default(false)->after('status');
        });

        Schema::table('login_attempts', function (Blueprint $table): void {
            $table->string('school_code', 20)->nullable()->after('identifier');
        });

        $this->backfillSchools();
        $this->backfillUsers();
        $this->backfillUserSchoolAssignments();
    }

    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table): void {
            $table->dropColumn('school_code');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'is_central']);
        });

        Schema::table('schools', function (Blueprint $table): void {
            $table->dropUnique(['school_code']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['school_code', 'slug']);
        });
    }

    private function backfillSchools(): void
    {
        $schools = DB::table('schools')->select(['id', 'stage', 'school_code', 'slug'])->orderBy('id')->get();

        foreach ($schools as $school) {
            $schoolCode = $school->school_code ?: $this->buildSchoolCode((int) $school->id, (string) ($school->stage ?? ''));
            $slug = $school->slug ?: Str::lower($schoolCode);

            DB::table('schools')
                ->where('id', $school->id)
                ->update([
                    'school_code' => $schoolCode,
                    'slug' => $slug,
                ]);
        }
    }

    private function backfillUsers(): void
    {
        $roleNames = DB::table('roles')->pluck('name', 'id');
        $users = DB::table('users')->select(['id', 'role_id', 'email', 'phone', 'username'])->orderBy('id')->get();

        foreach ($users as $user) {
            $roleName = (string) ($roleNames[$user->role_id] ?? '');
            $username = $user->username ?: $this->buildUsername(
                userId: (int) $user->id,
                email: $user->email,
                phone: $user->phone,
            );

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'username' => $username,
                    'is_central' => in_array($roleName, ['super_admin', 'admin', 'supervisor'], true),
                ]);
        }
    }

    private function backfillUserSchoolAssignments(): void
    {
        $existingAssignments = DB::table('user_school_assignments')
            ->select(['user_id', 'school_id'])
            ->get()
            ->map(static fn (object $row): string => $row->user_id . ':' . $row->school_id)
            ->all();

        $roleNames = DB::table('roles')->pluck('name', 'id');
        $users = DB::table('users')
            ->select(['id', 'role_id', 'school_id'])
            ->whereNotNull('school_id')
            ->get();

        foreach ($users as $user) {
            $key = $user->id . ':' . $user->school_id;

            if (in_array($key, $existingAssignments, true)) {
                continue;
            }

            $roleName = (string) ($roleNames[$user->role_id] ?? '');
            $assignmentType = $roleName === 'supervisor' ? 'supervising' : 'member';

            DB::table('user_school_assignments')->insert([
                'user_id' => $user->id,
                'school_id' => $user->school_id,
                'assignment_type' => $assignmentType,
                'created_at' => now(),
            ]);
        }
    }

    private function buildSchoolCode(int $schoolId, string $stage): string
    {
        $stageKey = match (trim($stage)) {
            'ثانوي' => 'S',
            'متوسط' => 'I',
            default => 'P',
        };

        return sprintf('JED-%s-%05d', $stageKey, $schoolId);
    }

    private function buildUsername(int $userId, ?string $email, ?string $phone): string
    {
        $base = null;

        if (is_string($email) && $email !== '') {
            $base = Str::of($email)->before('@')->lower()->replaceMatches('/[^a-z0-9._-]/', '');
        } elseif (is_string($phone) && $phone !== '') {
            $base = Str::of($phone)->replaceMatches('/[^0-9]/', '');
        }

        $base = trim((string) $base);

        if ($base === '') {
            $base = 'user';
        }

        $candidate = $base;

        if (DB::table('users')->where('username', $candidate)->where('id', '!=', $userId)->exists()) {
            $candidate = $base . $userId;
        }

        return Str::limit($candidate, 60, '');
    }
};
