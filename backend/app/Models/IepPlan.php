<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IepPlan extends Model
{
    use BelongsToSchool;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'academic_year_id',
        'teacher_user_id',
        'principal_user_id',
        'supervisor_user_id',
        'current_version_number',
        'status',
        'title',
        'start_date',
        'end_date',
        'summary',
        'strengths',
        'needs',
        'accommodations',
        'generated_pdf_file_id',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'accommodations' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(IepPlanGoal::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(IepPlanComment::class)->latest();
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(IepPlanApproval::class)->latest('created_at');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(IepPlanVersion::class)->latest('version_number');
    }
}
