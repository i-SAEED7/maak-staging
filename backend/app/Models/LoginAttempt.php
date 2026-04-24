<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'identifier',
        'school_code',
        'ip_address',
        'user_agent',
        'success',
        'attempted_at',
        'locked_until',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'attempted_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }
}
