<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name_ar',
        'name_en',
        'school_code',
        'slug',
        'ministry_code',
        'stage',
        'program_type',
        'gender',
        'region',
        'city',
        'district',
        'address',
        'location_lat',
        'location_lng',
        'phone',
        'email',
        'latitude',
        'longitude',
        'status',
        'storage_quota_mb',
        'principal_user_id',
        'principal_id',
        'supervisor_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'location_lat' => 'float',
            'location_lng' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'deleted_at' => 'datetime',
        ];
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
