<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_approval_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('first_name', 100);
            $table->string('second_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('email', 150);
            $table->string('phone', 30);
            $table->string('account_type', 30)->nullable();
            $table->string('password_hash');
            $table->string('stage', 100);
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'stage']);
            $table->index(['school_id', 'status']);
            $table->index('email');
        });

        Schema::create('audit_log_archives', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('original_audit_log_id')->nullable()->index();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('target_type', 100)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('endpoint')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();
        });

        Schema::create('education_program_school', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('education_program_id')->constrained('education_programs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'education_program_id'], 'school_program_unique');
        });

        $this->backfillSchoolPrograms();
        $this->seedTeacherPortfolioPermissions();
    }

    public function down(): void
    {
        DB::table('role_permissions')
            ->whereIn('permission_id', function ($query): void {
                $query->select('id')
                    ->from('permissions')
                    ->where('module', 'teacher_portfolios');
            })
            ->delete();

        DB::table('permissions')->where('module', 'teacher_portfolios')->delete();

        Schema::dropIfExists('education_program_school');
        Schema::dropIfExists('audit_log_archives');
        Schema::dropIfExists('account_approval_requests');
    }

    private function backfillSchoolPrograms(): void
    {
        $programsByName = DB::table('education_programs')
            ->pluck('id', 'name_ar')
            ->all();

        DB::table('schools')
            ->select(['id', 'program_type'])
            ->whereNotNull('program_type')
            ->orderBy('id')
            ->get()
            ->each(function (object $school) use ($programsByName): void {
                $programNames = collect(preg_split('/،|,/', (string) $school->program_type) ?: [])
                    ->map(static fn (string $name): string => trim($name))
                    ->filter()
                    ->unique()
                    ->values();

                foreach ($programNames as $programName) {
                    $programId = $programsByName[$programName] ?? null;

                    if (! $programId) {
                        continue;
                    }

                    DB::table('education_program_school')->updateOrInsert([
                        'school_id' => $school->id,
                        'education_program_id' => $programId,
                    ], [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    private function seedTeacherPortfolioPermissions(): void
    {
        $permissions = [
            ['key' => 'teacher_portfolios.view', 'display_name_ar' => 'عرض ملف إنجاز المعلم'],
            ['key' => 'teacher_portfolios.create', 'display_name_ar' => 'إنشاء ملف إنجاز المعلم'],
            ['key' => 'teacher_portfolios.update', 'display_name_ar' => 'تعديل ملف إنجاز المعلم'],
            ['key' => 'teacher_portfolios.delete', 'display_name_ar' => 'حذف ملف إنجاز المعلم'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert([
                'key' => $permission['key'],
            ], [
                'display_name_ar' => $permission['display_name_ar'],
                'module' => 'teacher_portfolios',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->value('id');

        if (! $teacherRoleId) {
            return;
        }

        DB::table('permissions')
            ->where('module', 'teacher_portfolios')
            ->pluck('id')
            ->each(function (int $permissionId) use ($teacherRoleId): void {
                DB::table('role_permissions')->updateOrInsert([
                    'role_id' => $teacherRoleId,
                    'permission_id' => $permissionId,
                ], [
                    'granted_at' => now(),
                ]);
            });
    }
};
