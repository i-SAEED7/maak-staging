<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Message extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'uuid',
        'school_id',
        'thread_key',
        'sender_user_id',
        'subject',
        'body',
        'parent_message_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_message_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }
}
