<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Notification extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'uuid',
        'school_id',
        'user_id',
        'created_by_user_id',
        'type',
        'channel',
        'title',
        'body',
        'data',
        'read_at',
        'sent_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
