<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IepPlanGoal extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'iep_plan_id',
        'school_id',
        'domain',
        'goal_text',
        'measurement_method',
        'baseline_value',
        'target_value',
        'due_date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function iepPlan(): BelongsTo
    {
        return $this->belongsTo(IepPlan::class);
    }
}
