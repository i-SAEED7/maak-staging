<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationProgram;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class PortalController extends Controller
{
    public function programs(): JsonResponse
    {
        $programs = EducationProgram::query()
            ->where('is_active', true)
            ->orderBy('name_ar')
            ->get()
            ->map(fn (EducationProgram $program): array => [
                'id' => $program->id,
                'code' => $program->code,
                'name_ar' => $program->name_ar,
                'description' => $program->description,
            ])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب برامج البوابة',
            'data' => $programs,
        ]);
    }

    public function schools(): JsonResponse
    {
        $schools = School::query()
            ->where('status', 'active')
            ->orderBy('name_ar')
            ->get()
            ->map(function (School $school): array {
                $locationLat = $school->location_lat ?? $school->latitude;
                $locationLng = $school->location_lng ?? $school->longitude;

                return [
                    'id' => $school->id,
                    'name_ar' => $school->name_ar,
                    'school_code' => $school->school_code,
                    'slug' => $school->slug,
                    'official_code' => $school->school_code ?: $school->ministry_code,
                    'stage' => $school->stage,
                    'program_type' => $school->program_type,
                    'gender' => $school->gender,
                    'city' => $school->city,
                    'address' => $school->address,
                    'location_lat' => $locationLat,
                    'location_lng' => $locationLng,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب مدارس البوابة',
            'data' => $schools,
        ]);
    }

    public function statistics(): JsonResponse
    {
        $activeSchoolIds = School::query()
            ->where('status', 'active')
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $programBreakdown = School::query()
            ->select('program_type', DB::raw('count(*) as schools_count'))
            ->where('status', 'active')
            ->whereNotNull('program_type')
            ->groupBy('program_type')
            ->orderBy('program_type')
            ->get()
            ->map(fn (School $school): array => [
                'program_type' => $school->program_type,
                'schools_count' => (int) $school->schools_count,
            ])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب إحصائيات البوابة',
            'data' => [
                'schools_count' => count($activeSchoolIds),
                'programs_count' => (int) EducationProgram::query()->where('is_active', true)->count(),
                'students_count' => (int) DB::table('students')
                    ->whereIn('school_id', $activeSchoolIds)
                    ->where('enrollment_status', 'active')
                    ->count(),
                'teachers_count' => (int) DB::table('users')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('roles.name', 'teacher')
                    ->where('users.status', 'active')
                    ->whereIn('users.school_id', $activeSchoolIds)
                    ->count(),
                'program_breakdown' => $programBreakdown,
            ],
        ]);
    }
}
