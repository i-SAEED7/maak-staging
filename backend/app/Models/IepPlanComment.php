<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IepPlanComment extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'iep_plan_id',
        'school_id',
        'author_user_id',
        'target_section',
        'comment_text',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function iepPlan(): BelongsTo
    {
        return $this->belongsTo(IepPlan::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
