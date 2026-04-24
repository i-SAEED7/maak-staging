<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FileAccessToken extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'file_id',
        'token_hash',
        'issued_to_user_id',
        'expires_at',
        'consumed_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_to_user_id');
    }
}
