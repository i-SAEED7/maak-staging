<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SupervisorVisit extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'uuid',
        'school_id',
        'supervisor_user_id',
        'template_id',
        'visit_date',
        'visit_status',
        'agenda',
        'summary',
        'overall_score',
        'next_follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'overall_score' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupervisorVisitItem::class, 'visit_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(SupervisorVisitRecommendation::class, 'visit_id');
    }
}
