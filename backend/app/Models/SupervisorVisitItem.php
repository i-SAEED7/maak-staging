<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupervisorVisitItem extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'visit_id',
        'school_id',
        'criterion_key',
        'criterion_label',
        'score',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(SupervisorVisit::class, 'visit_id');
    }
}
