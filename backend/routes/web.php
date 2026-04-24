<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\DisabilityCategory;
use App\Models\EducationProgram;
use App\Models\File;
use App\Models\IepPlan;
use App\Models\User;
use App\Http\Controllers\Api\V1\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $demoSchool = School::query()->orderBy('id')->first();
    $messagingTargets = $demoSchool
        ? User::query()
            ->with('role')
            ->where(function ($query) use ($demoSchool): void {
                $query
                    ->where('school_id', $demoSchool->id)
                    ->orWhereHas('schoolAssignments', fn ($assignmentQuery) => $assignmentQuery->where('school_id', $demoSchool->id));
            })
            ->orderBy('full_name')
            ->get(['id', 'role_id', 'school_id', 'full_name', 'email'])
            ->map(static fn (User $user): array => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role?->name,
                'school_id' => $user->school_id,
            ])
            ->values()
            ->all()
        : [];

    $demoAccounts = [
        [
            'label' => 'Super Admin',
            'email' => 'superadmin@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
        [
            'label' => 'Admin',
            'email' => 'admin@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
        [
            'label' => 'Supervisor',
            'email' => 'supervisor@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
        [
            'label' => 'Principal',
            'email' => 'principal@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
        [
            'label' => 'Teacher',
            'email' => 'teacher@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
        [
            'label' => 'Parent',
            'email' => 'parent@maak.local',
            'password' => 'Password@123',
            'school_id' => $demoSchool?->id,
        ],
    ];

    return view('preview', [
        'appName' => 'MAAK System Preview',
        'stats' => [
            'schools' => School::query()->count(),
            'users' => User::query()->count(),
            'students' => Student::withoutGlobalScopes()->count(),
            'iep_plans' => IepPlan::withoutGlobalScopes()->count(),
            'files' => File::withoutGlobalScopes()->count(),
        ],
        'demoSchool' => [
            'id' => $demoSchool?->id,
            'name_ar' => $demoSchool?->name_ar ?? 'لم تُزرع مدرسة Demo بعد',
        ],
        'demoAccounts' => $demoAccounts,
        'referenceData' => [
            'roles' => Role::query()
                ->orderBy('name')
                ->get(['id', 'name', 'display_name_ar'])
                ->toArray(),
            'schools' => School::query()
                ->orderBy('name_ar')
                ->get(['id', 'name_ar', 'city', 'region', 'status'])
                ->toArray(),
            'academic_years' => AcademicYear::query()
                ->orderByDesc('is_active')
                ->orderBy('name_ar')
                ->get(['id', 'school_id', 'name_ar', 'is_active'])
                ->toArray(),
            'education_programs' => EducationProgram::query()
                ->where('is_active', true)
                ->orderBy('name_ar')
                ->get(['id', 'code', 'name_ar'])
                ->toArray(),
            'disability_categories' => DisabilityCategory::query()
                ->where('is_active', true)
                ->orderBy('name_ar')
                ->get(['id', 'code', 'name_ar'])
                ->toArray(),
            'messaging_targets' => $messagingTargets,
        ],
    ]);
});

Route::get('/temporary-files/{token}', [FileController::class, 'downloadTemporary'])
    ->name('temporary-files.download');
