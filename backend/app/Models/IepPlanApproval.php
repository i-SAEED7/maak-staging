<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IepPlanApproval extends Model
{
    use BelongsToSchool;

    public $timestamps = false;

    protected $fillable = [
        'iep_plan_id',
        'school_id',
        'action_by_user_id',
        'action_role',
        'from_status',
        'to_status',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function iepPlan(): BelongsTo
    {
        return $this->belongsTo(IepPlan::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }
}
