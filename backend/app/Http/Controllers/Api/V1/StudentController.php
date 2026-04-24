<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveStudentRequest;
use App\Http\Requests\AssignGuardianRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Student::class);
        $students = $this->studentService->paginate($request->input('filter', []));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الطلاب',
            'data' => StudentResource::collection($students->items()),
            'meta' => [
                'page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $this->authorize('create', Student::class);
        $student = $this->studentService->create($request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $student,
            [],
            $student->only(['full_name', 'student_number', 'school_id', 'grade_level', 'enrollment_status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الطالب',
            'data' => new StudentResource($student),
        ], 201);
    }

    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);
        $this->studentService->assertAccessible($student, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب بيانات الطالب',
            'data' => new StudentResource($this->studentService->guardians($student)),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);
        $this->studentService->assertAccessible($student, $request->user());
        $before = $student->only(['full_name', 'student_number', 'school_id', 'grade_level', 'enrollment_status']);
        $student = $this->studentService->update($student, $request->validated());
        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $student,
            $before,
            $student->only(['full_name', 'student_number', 'school_id', 'grade_level', 'enrollment_status']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الطالب',
            'data' => new StudentResource($student),
        ]);
    }

    public function archive(ArchiveStudentRequest $request, Student $student): JsonResponse
    {
        $this->authorize('archive', $student);
        $this->studentService->assertAccessible($student, $request->user());
        $before = $student->only(['full_name', 'student_number', 'enrollment_status', 'archived_at']);
        $student = $this->studentService->archive($student, $request->validated('reason'));
        app(AuditLogger::class)->log(
            $request->user(),
            'delete',
            $student,
            $before,
            $student->only(['full_name', 'student_number', 'enrollment_status', 'archived_at']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تمت أرشفة الطالب',
            'data' => new StudentResource($student),
        ]);
    }

    public function guardians(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);
        $this->studentService->assertAccessible($student, $request->user());
        $student = $this->studentService->guardians($student);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب أولياء الأمور',
            'data' => [
                'student' => new StudentResource($student),
                'guardians' => (new StudentResource($student))->toArray($request)['guardians'],
            ],
        ]);
    }

    public function assignGuardian(AssignGuardianRequest $request, Student $student): JsonResponse
    {
        $this->authorize('assignGuardian', $student);
        $this->studentService->assertAccessible($student, $request->user());
        $guardian = $this->studentService->assignGuardian($student, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم ربط ولي الأمر بالطالب',
            'data' => [
                'id' => $guardian->id,
                'parent_user_id' => $guardian->parent_user_id,
                'parent_name' => $guardian->parent?->full_name,
                'relationship' => $guardian->relationship,
                'is_primary' => $guardian->is_primary,
                'can_view_reports' => $guardian->can_view_reports,
                'can_message_school' => $guardian->can_message_school,
            ],
        ], 201);
    }
}
