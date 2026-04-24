<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationProgramRequest;
use App\Http\Requests\UpdateEducationProgramRequest;
use App\Models\EducationProgram;
use App\Support\AuditLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class EducationProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $programs = EducationProgram::query()
            ->when(
                ! $request->boolean('include_inactive'),
                fn ($query) => $query->where('is_active', true),
            )
            ->orderBy('name_ar')
            ->get(['id', 'code', 'name_ar', 'is_active']);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب البرامج التعليمية',
            'data' => $programs,
        ]);
    }

    public function store(StoreEducationProgramRequest $request): JsonResponse
    {
        $this->authorizeManagePrograms($request);

        $program = EducationProgram::query()->create([
            'code' => $request->validated('code') ?: $this->generateCode($request->validated('name_ar')),
            'name_ar' => $request->validated('name_ar'),
            'is_active' => (bool) $request->validated('is_active', true),
        ]);

        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $program,
            [],
            $program->only(['name_ar', 'code', 'is_active']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء البرنامج التعليمي',
            'data' => $program,
        ], 201);
    }

    public function update(UpdateEducationProgramRequest $request, EducationProgram $educationProgram): JsonResponse
    {
        $this->authorizeManagePrograms($request);
        $before = $educationProgram->only(['name_ar', 'code', 'is_active']);

        $educationProgram->update([
            'code' => $request->validated('code', $educationProgram->code),
            'name_ar' => $request->validated('name_ar', $educationProgram->name_ar),
            'is_active' => $request->has('is_active')
                ? (bool) $request->validated('is_active')
                : $educationProgram->is_active,
        ]);

        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $educationProgram,
            $before,
            $educationProgram->only(['name_ar', 'code', 'is_active']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث البرنامج التعليمي',
            'data' => $educationProgram->refresh(),
        ]);
    }

    public function destroy(Request $request, EducationProgram $educationProgram): JsonResponse
    {
        $this->authorizeManagePrograms($request);
        $before = $educationProgram->only(['is_active']);
        $educationProgram->update(['is_active' => false]);

        app(AuditLogger::class)->log(
            $request->user(),
            'delete',
            $educationProgram,
            $before,
            $educationProgram->only(['is_active']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل البرنامج التعليمي',
            'data' => $educationProgram->refresh(),
        ]);
    }

    private function authorizeManagePrograms(Request $request): void
    {
        $user = $request->user();

        if ($user?->role?->name === 'super_admin' || $user?->hasPermission('reference_data.manage')) {
            return;
        }

        throw new AuthorizationException('لا تملك صلاحية إدارة البرامج التعليمية.');
    }

    private function generateCode(string $name): string
    {
        $base = Str::upper(Str::slug($name, '_'));

        if ($base === '') {
            $base = 'PROGRAM';
        }

        $candidate = $base;
        $counter = 1;

        while (EducationProgram::query()->where('code', $candidate)->exists()) {
            $counter += 1;
            $candidate = sprintf('%s_%02d', $base, $counter);
        }

        return $candidate;
    }
}
