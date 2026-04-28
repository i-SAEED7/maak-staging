<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccountApprovalRequest extends Model
{
    protected $fillable = [
        'uuid',
        'first_name',
        'second_name',
        'last_name',
        'email',
        'phone',
        'account_type',
        'password_hash',
        'stage',
        'school_id',
        'status',
        'approved_by_user_id',
        'created_user_id',
        'approved_at',
        'metadata',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'approved_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function fullName(): string
    {
        return collect([$this->first_name, $this->second_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }
}
