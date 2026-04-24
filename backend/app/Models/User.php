<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'uuid',
        'role_id',
        'school_id',
        'full_name',
        'username',
        'national_id_encrypted',
        'email',
        'phone',
        'password_hash',
        'status',
        'is_central',
        'locale',
        'must_change_password',
        'two_factor_enabled',
        'profile_photo_file_id',
        'metadata',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_central' => 'boolean',
            'must_change_password' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolAssignments(): HasMany
    {
        return $this->hasMany(UserSchoolAssignment::class);
    }

    public function assignedSchools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'user_school_assignments')
            ->withPivot('assignment_type');
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('effect')
            ->withTimestamps();
    }

    public function permissionKeys(): array
    {
        $this->loadMissing('role.permissions', 'directPermissions');

        if ($this->role?->name === 'super_admin') {
            return ['*'];
        }

        $rolePermissionKeys = $this->role?->permissions?->pluck('key')->values()->all() ?? [];
        $allowedPermissionKeys = $this->directPermissions
            ->filter(static fn (Permission $permission): bool => $permission->pivot?->effect === 'allow')
            ->pluck('key')
            ->values()
            ->all();
        $deniedPermissionKeys = $this->directPermissions
            ->filter(static fn (Permission $permission): bool => $permission->pivot?->effect === 'deny')
            ->pluck('key')
            ->values()
            ->all();

        return collect([...$rolePermissionKeys, ...$allowedPermissionKeys])
            ->reject(static fn (string $permissionKey): bool => in_array($permissionKey, $deniedPermissionKeys, true))
            ->unique()
            ->values()
            ->all();
    }

    public function hasPermission(string $permission): bool
    {
        $permissionKeys = $this->permissionKeys();

        return in_array('*', $permissionKeys, true)
            || in_array($permission, $permissionKeys, true);
    }

    public function usesCentralAccess(): bool
    {
        return (bool) $this->is_central;
    }

    public function usesSchoolAccess(): bool
    {
        return ! $this->usesCentralAccess();
    }
}
