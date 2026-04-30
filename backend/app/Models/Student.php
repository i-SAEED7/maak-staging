<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasEncryptedFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use BelongsToSchool;
    use HasEncryptedFields;
    use SoftDeletes;

    /**
     * Fields that contain PII and must be encrypted at rest.
     */
    protected array $encryptedFields = [
        'national_id_encrypted',
        'medical_notes',
        'social_notes',
    ];

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_year_id',
        'education_program_id',
        'disability_category_id',
        'primary_teacher_user_id',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name',
        'full_name',
        'national_id_encrypted',
        'student_number',
        'gender',
        'birth_date',
        'grade_level',
        'classroom',
        'enrollment_status',
        'medical_notes',
        'social_notes',
        'transportation_notes',
        'joined_at',
        'archived_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'joined_at' => 'date',
            'archived_at' => 'datetime',
            'medical_notes' => 'array',
            'social_notes' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function educationProgram(): BelongsTo
    {
        return $this->belongsTo(EducationProgram::class);
    }

    public function disabilityCategory(): BelongsTo
    {
        return $this->belongsTo(DisabilityCategory::class);
    }

    public function primaryTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_teacher_user_id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function guardianUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_guardians', 'student_id', 'parent_user_id')
            ->withPivot(['relationship', 'is_primary', 'can_view_reports', 'can_message_school'])
            ->withTimestamps();
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherStudentAssignment::class);
    }

    public function iepPlans(): HasMany
    {
        return $this->hasMany(IepPlan::class);
    }
}
