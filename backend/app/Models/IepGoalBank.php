<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pre-built educational goals and strategies organized by disability type.
 *
 * Teachers can select from this bank when creating IEP plans,
 * significantly reducing authoring time and standardizing quality.
 */
class IepGoalBank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'disability_category_id',
        'domain',
        'goal_text',
        'strategies',
        'suggested_criteria',
        'grade_level_min',
        'grade_level_max',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'strategies' => 'array',
            'suggested_criteria' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function disabilityCategory(): BelongsTo
    {
        return $this->belongsTo(DisabilityCategory::class);
    }

    /**
     * Scope: only active goals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter by disability category.
     */
    public function scopeForDisability($query, int $disabilityCategoryId)
    {
        return $query->where('disability_category_id', $disabilityCategoryId);
    }

    /**
     * Scope: filter by grade level range.
     */
    public function scopeForGradeLevel($query, int $gradeLevel)
    {
        return $query
            ->where('grade_level_min', '<=', $gradeLevel)
            ->where('grade_level_max', '>=', $gradeLevel);
    }
}
