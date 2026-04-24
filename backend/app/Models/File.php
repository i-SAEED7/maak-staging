<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class File extends Model
{
    use BelongsToSchool;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'uploaded_by_user_id',
        'related_type',
        'related_id',
        'category',
        'original_name',
        'storage_name',
        'storage_disk',
        'storage_path',
        'mime_type',
        'extension',
        'size_bytes',
        'checksum_sha256',
        'is_sensitive',
        'visibility',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
            'uploaded_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(FileAccessToken::class);
    }
}
