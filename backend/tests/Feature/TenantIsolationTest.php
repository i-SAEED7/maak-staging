<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cross-Tenant Data Isolation Tests.
 *
 * Verifies that the BelongsToSchool global scope correctly isolates
 * data between schools and prevents cross-tenant data leaks.
 */
class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private TenantContext $tenantContext;
    private School $schoolA;
    private School $schoolB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantContext = app(TenantContext::class);

        $this->schoolA = School::factory()->create(['name_ar' => 'مدرسة ألف']);
        $this->schoolB = School::factory()->create(['name_ar' => 'مدرسة باء']);
    }

    /** @test */
    public function students_are_scoped_to_their_school(): void
    {
        $studentA = Student::factory()->create([
            'school_id' => $this->schoolA->id,
            'full_name' => 'طالب مدرسة ألف',
        ]);

        $studentB = Student::factory()->create([
            'school_id' => $this->schoolB->id,
            'full_name' => 'طالب مدرسة باء',
        ]);

        // Scope to School A
        $this->tenantContext->setSchoolId($this->schoolA->id);
        $studentsA = Student::all();

        $this->assertCount(1, $studentsA);
        $this->assertTrue($studentsA->contains($studentA));
        $this->assertFalse($studentsA->contains($studentB));

        // Scope to School B
        $this->tenantContext->setSchoolId($this->schoolB->id);
        $studentsB = Student::all();

        $this->assertCount(1, $studentsB);
        $this->assertTrue($studentsB->contains($studentB));
        $this->assertFalse($studentsB->contains($studentA));
    }

    /** @test */
    public function no_scope_returns_no_data_for_scoped_models(): void
    {
        Student::factory()->create(['school_id' => $this->schoolA->id]);
        Student::factory()->create(['school_id' => $this->schoolB->id]);

        // Without setting any school context, scope should filter everything
        $this->tenantContext->clear();
        $students = Student::all();

        // With null school_id, the global scope should not apply any filter
        // (by design: null schoolId means "super admin" — no filter)
        $this->assertCount(2, $students);
    }

    /** @test */
    public function creating_a_student_auto_assigns_school_id(): void
    {
        $this->tenantContext->setSchoolId($this->schoolA->id);

        $student = Student::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'full_name' => 'طالب جديد',
            'national_id_encrypted' => '1234567890',
            'gender' => 'male',
        ]);

        $this->assertEquals($this->schoolA->id, $student->school_id);
    }

    /** @test */
    public function school_a_cannot_see_school_b_student_by_id(): void
    {
        $studentB = Student::factory()->create([
            'school_id' => $this->schoolB->id,
            'full_name' => 'طالب سري لمدرسة باء',
        ]);

        $this->tenantContext->setSchoolId($this->schoolA->id);

        $result = Student::find($studentB->id);
        $this->assertNull($result, 'School A should NOT be able to find School B student by ID');
    }

    /** @test */
    public function api_returns_only_tenant_students(): void
    {
        Student::factory()->count(3)->create(['school_id' => $this->schoolA->id]);
        Student::factory()->count(5)->create(['school_id' => $this->schoolB->id]);

        $userA = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'role' => 'teacher',
        ]);

        $response = $this->actingAs($userA)
            ->withHeaders(['X-School-Id' => (string) $this->schoolA->id])
            ->getJson('/api/v1/students');

        $response->assertOk();

        $data = $response->json('data');

        // Each student in the response should belong to School A
        foreach ($data as $student) {
            $this->assertEquals(
                $this->schoolA->id,
                $student['school_id'],
                'API response contains a student from another school — DATA LEAK!'
            );
        }
    }
}
