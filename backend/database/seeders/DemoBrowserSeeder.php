<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\IepPlanStatus;
use App\Enums\RoleName;
use App\Models\AcademicYear;
use App\Models\DisabilityCategory;
use App\Models\EducationProgram;
use App\Models\File;
use App\Models\IepPlan;
use App\Models\IepPlanGoal;
use App\Models\IepPlanVersion;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Notification;
use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\SupervisorVisit;
use App\Models\SupervisorVisitItem;
use App\Models\SupervisorVisitRecommendation;
use App\Models\User;
use App\Models\UserSchoolAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoBrowserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            ReferenceDataSeeder::class,
        ]);

        $school = School::query()->updateOrCreate(
            ['school_code' => 'JED-P-00001'],
            [
                'uuid' => (string) Str::uuid(),
                'name_ar' => 'مدرسة النور لذوي الإعاقة',
                'name_en' => 'Al Noor Disability Support School',
                'school_code' => 'JED-P-00001',
                'slug' => 'jed-p-00001',
                'ministry_code' => 'MAAK-DEMO-001',
                'region' => 'الرياض',
                'city' => 'الرياض',
                'district' => 'العليا',
                'address' => 'حي العليا، الرياض',
                'stage' => 'ابتدائي',
                'program_type' => 'يسير التعليمي',
                'gender' => 'بنين',
                'location_lat' => 24.7135517,
                'location_lng' => 46.6752957,
                'latitude' => 24.7135517,
                'longitude' => 46.6752957,
                'phone' => '0110000001',
                'email' => 'school-demo@maak.local',
                'status' => 'active',
                'storage_quota_mb' => 4096,
            ],
        );

        $roles = Role::query()->whereIn('name', [
            RoleName::SUPER_ADMIN,
            RoleName::ADMIN,
            RoleName::SUPERVISOR,
            RoleName::PRINCIPAL,
            RoleName::TEACHER,
            RoleName::PARENT,
        ])->get()->keyBy('name');

        $superAdmin = $this->upsertUser(
            roleId: (int) $roles[RoleName::SUPER_ADMIN]->id,
            fullName: 'مدير النظام التجريبي',
            email: 'superadmin@maak.local',
            phone: '0500000001',
            schoolId: null,
            username: 'superadmin',
            isCentral: true,
        );

        $admin = $this->upsertUser(
            roleId: (int) $roles[RoleName::ADMIN]->id,
            fullName: 'الإداري التجريبي',
            email: 'admin@maak.local',
            phone: '0500000006',
            schoolId: null,
            username: 'admin',
            isCentral: true,
        );

        $supervisor = $this->upsertUser(
            roleId: (int) $roles[RoleName::SUPERVISOR]->id,
            fullName: 'المشرف التجريبي',
            email: 'supervisor@maak.local',
            phone: '0500000002',
            schoolId: null,
            username: 'supervisor',
            isCentral: true,
        );

        $principal = $this->upsertUser(
            roleId: (int) $roles[RoleName::PRINCIPAL]->id,
            fullName: 'مدير المدرسة التجريبي',
            email: 'principal@maak.local',
            phone: '0500000003',
            schoolId: (int) $school->id,
            username: 'principal',
            isCentral: false,
        );

        $teacher = $this->upsertUser(
            roleId: (int) $roles[RoleName::TEACHER]->id,
            fullName: 'المعلم التجريبي',
            email: 'teacher@maak.local',
            phone: '0500000004',
            schoolId: (int) $school->id,
            username: 'teacher',
            isCentral: false,
        );

        $parent = $this->upsertUser(
            roleId: (int) $roles[RoleName::PARENT]->id,
            fullName: 'ولي الأمر التجريبي',
            email: 'parent@maak.local',
            phone: '0500000005',
            schoolId: (int) $school->id,
            username: 'parent',
            isCentral: false,
        );

        $school->update([
            'principal_id' => $principal->id,
            'principal_user_id' => $principal->id,
            'supervisor_id' => $supervisor->id,
        ]);

        UserSchoolAssignment::query()->whereIn('user_id', [
            $principal->id,
            $teacher->id,
            $parent->id,
        ])->delete();

        foreach ([$principal, $teacher, $parent] as $schoolUser) {
            UserSchoolAssignment::query()->updateOrCreate(
                [
                    'user_id' => $schoolUser->id,
                    'school_id' => $school->id,
                ],
                [
                    'assignment_type' => 'member',
                    'created_at' => now(),
                ],
            );
        }

        UserSchoolAssignment::query()->updateOrCreate(
            [
                'user_id' => $supervisor->id,
                'school_id' => $school->id,
            ],
            [
                'assignment_type' => 'supervising',
                'created_at' => now(),
            ],
        );

        School::query()
            ->where('id', '!=', $school->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->limit(2)
            ->get()
            ->each(function (School $assignedSchool) use ($supervisor): void {
                if ($assignedSchool->supervisor_id === null) {
                    $assignedSchool->update(['supervisor_id' => $supervisor->id]);
                }

                UserSchoolAssignment::query()->updateOrCreate(
                    [
                        'user_id' => $supervisor->id,
                        'school_id' => $assignedSchool->id,
                    ],
                    [
                        'assignment_type' => 'supervising',
                        'created_at' => now(),
                    ],
                );
            });

        $academicYear = AcademicYear::query()->updateOrCreate(
            [
                'school_id' => $school->id,
                'name_ar' => '1447-1448',
            ],
            [
                'starts_on' => '2025-08-24',
                'ends_on' => '2026-06-25',
                'is_active' => true,
            ],
        );

        $program = EducationProgram::query()->where('is_active', true)->first();
        $disability = DisabilityCategory::query()->where('is_active', true)->first();

        $student = Student::withoutGlobalScopes()->updateOrCreate(
            [
                'school_id' => $school->id,
                'student_number' => 'ST-1001',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'academic_year_id' => $academicYear->id,
                'education_program_id' => $program?->id,
                'disability_category_id' => $disability?->id,
                'primary_teacher_user_id' => $teacher->id,
                'first_name' => 'سلمان',
                'father_name' => 'أحمد',
                'grandfather_name' => 'محمد',
                'family_name' => 'القحطاني',
                'full_name' => 'سلمان أحمد محمد القحطاني',
                'gender' => 'male',
                'birth_date' => '2015-04-16',
                'grade_level' => 'الرابع',
                'classroom' => '4A',
                'enrollment_status' => 'active',
                'medical_notes' => ['allergy' => 'none'],
                'social_notes' => ['needs_support' => false],
                'transportation_notes' => 'يحضر مع ولي الأمر',
                'joined_at' => '2025-08-25',
                'metadata' => ['seeded' => true],
            ],
        );

        StudentGuardian::query()->updateOrCreate(
            [
                'student_id' => $student->id,
                'parent_user_id' => $parent->id,
            ],
            [
                'relationship' => 'father',
                'is_primary' => true,
                'can_view_reports' => true,
                'can_message_school' => true,
            ],
        );

        $demoFileContent = implode(PHP_EOL, [
            'ملف تجريبي مرتبط بالطالب سلمان أحمد محمد القحطاني.',
            'الغرض: التحقق من وحدة الملفات عبر واجهة المتصفح والـ API.',
            'آخر تحديث: ' . now()->toDateTimeString(),
        ]);
        $demoStorageName = 'demo-student-progress.txt';
        $demoStoragePath = sprintf('maak/%d/demo/%s', $school->id, $demoStorageName);
        Storage::disk('local')->put($demoStoragePath, $demoFileContent);

        File::withoutGlobalScopes()->updateOrCreate(
            [
                'storage_name' => $demoStorageName,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'uploaded_by_user_id' => $teacher->id,
                'related_type' => Student::class,
                'related_id' => $student->id,
                'category' => 'portfolio',
                'original_name' => 'salman-progress-demo.txt',
                'storage_disk' => 'local',
                'storage_path' => $demoStoragePath,
                'mime_type' => 'text/plain',
                'extension' => 'txt',
                'size_bytes' => strlen($demoFileContent),
                'checksum_sha256' => hash('sha256', $demoFileContent),
                'is_sensitive' => false,
                'visibility' => 'school',
                'uploaded_at' => now()->subDay(),
                'deleted_at' => null,
            ],
        );

        $teacherParentMessage = Message::withoutGlobalScopes()->updateOrCreate(
            [
                'thread_key' => 'demo-thread-teacher-parent',
                'sender_user_id' => $teacher->id,
                'body' => 'مرحبًا، نود مشاركتكم ملاحظة إيجابية حول تقدم سلمان هذا الأسبوع داخل الحصة.',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'subject' => 'متابعة تقدم سلمان',
                'parent_message_id' => null,
            ],
        );

        MessageRecipient::query()->updateOrCreate(
            [
                'message_id' => $teacherParentMessage->id,
                'recipient_user_id' => $parent->id,
            ],
            [
                'read_at' => null,
                'created_at' => now()->subDays(2),
            ],
        );

        $parentReply = Message::withoutGlobalScopes()->updateOrCreate(
            [
                'thread_key' => 'demo-thread-teacher-parent',
                'sender_user_id' => $parent->id,
                'body' => 'شكرًا لكم، يسعدنا ذلك. هل توجد توصيات يمكن تطبيقها في المنزل أيضًا؟',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'subject' => 'متابعة تقدم سلمان',
                'parent_message_id' => $teacherParentMessage->id,
            ],
        );

        MessageRecipient::query()->updateOrCreate(
            [
                'message_id' => $parentReply->id,
                'recipient_user_id' => $teacher->id,
            ],
            [
                'read_at' => null,
                'created_at' => now()->subDay(),
            ],
        );

        $principalSupervisorMessage = Message::withoutGlobalScopes()->updateOrCreate(
            [
                'thread_key' => 'demo-thread-principal-supervisor',
                'sender_user_id' => $principal->id,
                'body' => 'تم رفع الخطة الفردية التجريبية، ونحتاج مراجعتكم خلال هذا الأسبوع.',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'subject' => 'مراجعة خطة فردية',
                'parent_message_id' => null,
            ],
        );

        MessageRecipient::query()->updateOrCreate(
            [
                'message_id' => $principalSupervisorMessage->id,
                'recipient_user_id' => $supervisor->id,
            ],
            [
                'read_at' => null,
                'created_at' => now()->subHours(8),
            ],
        );

        Notification::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id' => $teacher->id,
                'title' => 'تمت إضافة رسالة جديدة من ولي الأمر',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'created_by_user_id' => $parent->id,
                'type' => 'message.received',
                'channel' => 'in_app',
                'body' => 'هناك رد جديد في محادثة متابعة تقدم سلمان.',
                'data' => ['thread_key' => 'demo-thread-teacher-parent'],
                'read_at' => null,
                'sent_at' => now()->subHours(4),
                'failed_at' => null,
            ],
        );

        Notification::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id' => $principal->id,
                'title' => 'خطة IEP بانتظار اعتماد المدير',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'created_by_user_id' => $teacher->id,
                'type' => 'iep.pending_principal_review',
                'channel' => 'in_app',
                'body' => 'تم إرسال الخطة الفردية التجريبية وتنتظر مراجعتكم.',
                'data' => ['student_id' => $student->id],
                'read_at' => null,
                'sent_at' => now()->subHours(6),
                'failed_at' => null,
            ],
        );

        Notification::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id' => $supervisor->id,
                'title' => 'طلب مراجعة خطة فردية',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'school_id' => $school->id,
                'created_by_user_id' => $principal->id,
                'type' => 'iep.pending_supervisor_review',
                'channel' => 'in_app',
                'body' => 'هناك خطة فردية جديدة قادمة من مدير المدرسة للمراجعة.',
                'data' => ['thread_key' => 'demo-thread-principal-supervisor'],
                'read_at' => null,
                'sent_at' => now()->subHours(2),
                'failed_at' => null,
            ],
        );

        $visit = SupervisorVisit::withoutGlobalScopes()->updateOrCreate(
            [
                'school_id' => $school->id,
                'supervisor_user_id' => $supervisor->id,
                'visit_date' => '2026-04-20',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'template_id' => null,
                'visit_status' => 'scheduled',
                'agenda' => 'مراجعة تنفيذ الخطة الفردية ومتابعة البيئة الصفية للطالب سلمان.',
                'summary' => null,
                'overall_score' => null,
                'next_follow_up_at' => null,
            ],
        );

        SupervisorVisitItem::query()->updateOrCreate(
            [
                'visit_id' => $visit->id,
                'criterion_key' => 'classroom_environment',
            ],
            [
                'school_id' => $school->id,
                'criterion_label' => 'البيئة الصفية',
                'score' => 4,
                'remarks' => 'البيئة الصفية منظمة وتدعم التعلّم البصري.',
            ],
        );

        SupervisorVisitRecommendation::query()->updateOrCreate(
            [
                'visit_id' => $visit->id,
                'recommendation_text' => 'الاستمرار في استخدام الجداول البصرية وتوثيق تقدّم الطالب أسبوعيًا.',
            ],
            [
                'school_id' => $school->id,
                'owner_user_id' => $teacher->id,
                'due_date' => '2026-05-05',
                'status' => 'open',
                'completed_at' => null,
            ],
        );

        $plan = IepPlan::withoutGlobalScopes()->updateOrCreate(
            [
                'school_id' => $school->id,
                'student_id' => $student->id,
                'title' => 'الخطة الفردية التجريبية',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'academic_year_id' => $academicYear->id,
                'teacher_user_id' => $teacher->id,
                'principal_user_id' => $principal->id,
                'supervisor_user_id' => null,
                'current_version_number' => 1,
                'status' => IepPlanStatus::DRAFT,
                'start_date' => '2026-04-18',
                'end_date' => '2026-06-18',
                'summary' => 'خطة فردية تجريبية لقياس تدفق الاعتماد من المعلم إلى المدير ثم المشرف.',
                'strengths' => 'الطالب متجاوب مع التعليمات البصرية ويظهر تفاعلًا جيدًا داخل البيئة الصفية.',
                'needs' => 'تعزيز مهارات التواصل الوظيفي والتنظيم الذاتي أثناء أداء المهام اليومية.',
                'accommodations' => ['وقت إضافي', 'تعليمات مبسطة', 'دعم بصري'],
                'submitted_at' => null,
                'approved_at' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ],
        );

        IepPlanGoal::query()->updateOrCreate(
            [
                'iep_plan_id' => $plan->id,
                'sort_order' => 0,
            ],
            [
                'school_id' => $school->id,
                'domain' => 'التواصل',
                'goal_text' => 'أن يستخدم الطالب جملة من ثلاث كلمات للتعبير عن احتياجه في 4 من 5 مواقف صفية.',
                'measurement_method' => 'ملاحظة أسبوعية وقائمة متابعة',
                'baseline_value' => '40%',
                'target_value' => '80%',
                'due_date' => '2026-06-01',
            ],
        );

        IepPlanGoal::query()->updateOrCreate(
            [
                'iep_plan_id' => $plan->id,
                'sort_order' => 1,
            ],
            [
                'school_id' => $school->id,
                'domain' => 'الاستقلالية',
                'goal_text' => 'أن يُكمل الطالب مهمتين صفّيتين متتاليتين باستخدام جدول بصري دون تذكير مباشر.',
                'measurement_method' => 'سجل ملاحظة يومي',
                'baseline_value' => 'مرة واحدة يوميًا',
                'target_value' => 'مرتان يوميًا',
                'due_date' => '2026-06-10',
            ],
        );

        IepPlanVersion::query()->updateOrCreate(
            [
                'iep_plan_id' => $plan->id,
                'version_number' => 1,
            ],
            [
                'school_id' => $school->id,
                'content_json' => [
                    'title' => $plan->title,
                    'status' => $plan->status,
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'start_date' => '2026-04-18',
                    'end_date' => '2026-06-18',
                    'summary' => $plan->summary,
                    'strengths' => $plan->strengths,
                    'needs' => $plan->needs,
                    'accommodations' => $plan->accommodations,
                    'goals' => [
                        [
                            'domain' => 'التواصل',
                            'goal_text' => 'أن يستخدم الطالب جملة من ثلاث كلمات للتعبير عن احتياجه في 4 من 5 مواقف صفية.',
                            'measurement_method' => 'ملاحظة أسبوعية وقائمة متابعة',
                            'baseline_value' => '40%',
                            'target_value' => '80%',
                            'due_date' => '2026-06-01',
                            'sort_order' => 0,
                        ],
                        [
                            'domain' => 'الاستقلالية',
                            'goal_text' => 'أن يُكمل الطالب مهمتين صفّيتين متتاليتين باستخدام جدول بصري دون تذكير مباشر.',
                            'measurement_method' => 'سجل ملاحظة يومي',
                            'baseline_value' => 'مرة واحدة يوميًا',
                            'target_value' => 'مرتان يوميًا',
                            'due_date' => '2026-06-10',
                            'sort_order' => 1,
                        ],
                    ],
                ],
                'change_summary' => 'إصدار تجريبي أولي للخطة الفردية',
                'created_by_user_id' => $teacher->id,
                'created_at' => now(),
            ],
        );
    }

    private function upsertUser(
        int $roleId,
        string $fullName,
        string $email,
        string $phone,
        ?int $schoolId,
        string $username,
        bool $isCentral,
    ): User {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'uuid' => (string) Str::uuid(),
                'role_id' => $roleId,
                'school_id' => $schoolId,
                'full_name' => $fullName,
                'username' => $username,
                'phone' => $phone,
                'password_hash' => Hash::make('Password@123'),
                'status' => 'active',
                'is_central' => $isCentral,
                'locale' => 'ar',
                'must_change_password' => false,
                'two_factor_enabled' => false,
                'metadata' => ['seeded' => true],
            ],
        );
    }
}
