<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGuardian extends Model
{
    protected $fillable = [
        'student_id',
        'parent_user_id',
        'relationship',
        'is_primary',
        'can_view_reports',
        'can_message_school',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_message_school' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}
