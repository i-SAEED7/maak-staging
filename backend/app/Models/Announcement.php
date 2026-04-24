<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'created_by_user_id',
        'title',
        'body',
        'target_audience',
        'is_all_schools',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_all_schools' => 'boolean',
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class);
    }
}
