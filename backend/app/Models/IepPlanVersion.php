<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IepPlanVersion extends Model
{
    use BelongsToSchool;

    public $timestamps = false;

    protected $fillable = [
        'iep_plan_id',
        'school_id',
        'version_number',
        'content_json',
        'change_summary',
        'created_by_user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function iepPlan(): BelongsTo
    {
        return $this->belongsTo(IepPlan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
