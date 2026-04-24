<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AuthPortalException;
use App\Models\School;
use App\Models\User;
use App\Support\PermissionResolver;
use App\Support\LoginAttemptTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly PermissionResolver $permissionResolver = new PermissionResolver(),
        private readonly LoginAttemptTracker $loginAttemptTracker = new LoginAttemptTracker(),
    ) {
    }

    public function login(array $credentials, Request $request): array
    {
        return $this->authenticateAny($credentials['identifier'], $credentials['password'], $request);
    }

    public function loginCentral(array $credentials, Request $request): array
    {
        $identifier = $this->normalizeIdentifier($credentials['identifier']);
        $this->loginAttemptTracker->ensureNotLocked($identifier, null, $request->ip());

        $user = $this->findUserByIdentifier($identifier);

        if (! $this->canAuthenticate($user, $credentials['password'])) {
            $this->loginAttemptTracker->recordFailure($identifier, null, $request->ip(), $request->userAgent());
            throw new AuthPortalException('الحساب غير مصرح له بالدخول من هذا المسار', 422);
        }

        if (! $user->usesCentralAccess()) {
            $this->loginAttemptTracker->recordFailure($identifier, null, $request->ip(), $request->userAgent());
            throw new AuthPortalException('الحساب غير مصرح له بالدخول من هذا المسار', 403);
        }

        $this->loginAttemptTracker->recordSuccess($identifier, null, $request->ip(), $request->userAgent());

        return $this->issueLoginPayload($user, $request);
    }

    public function loginSchool(array $credentials, Request $request): array
    {
        $identifier = $this->normalizeIdentifier($credentials['identifier']);
        $schoolCode = $this->normalizeSchoolCode($credentials['school_code']);

        $this->loginAttemptTracker->ensureNotLocked($identifier, $schoolCode, $request->ip());

        $school = School::query()
            ->where('school_code', $schoolCode)
            ->where('status', 'active')
            ->first();
        $user = $this->findUserByIdentifier($identifier);

        if (! $school || ! $this->canAuthenticate($user, $credentials['password'])) {
            $this->loginAttemptTracker->recordFailure($identifier, $schoolCode, $request->ip(), $request->userAgent());
            throw new AuthPortalException('بيانات الدخول غير صحيحة أو غير مصرح بها', 422);
        }

        if ($user->usesCentralAccess() || ! $this->isUserLinkedToSchool($user, (int) $school->id)) {
            $this->loginAttemptTracker->recordFailure($identifier, $schoolCode, $request->ip(), $request->userAgent());
            throw new AuthPortalException('بيانات الدخول غير صحيحة أو غير مصرح بها', 403);
        }

        $this->loginAttemptTracker->recordSuccess($identifier, $schoolCode, $request->ip(), $request->userAgent());

        return $this->issueLoginPayload($user, $request);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    private function authenticateAny(string $identifier, string $password, Request $request): array
    {
        $normalizedIdentifier = $this->normalizeIdentifier($identifier);
        $this->loginAttemptTracker->ensureNotLocked($normalizedIdentifier, null, $request->ip());

        $user = $this->findUserByIdentifier($normalizedIdentifier);

        if (! $this->canAuthenticate($user, $password)) {
            $this->loginAttemptTracker->recordFailure($normalizedIdentifier, null, $request->ip(), $request->userAgent());
            throw new AuthPortalException('بيانات الدخول غير صحيحة أو غير مصرح بها', 422);
        }

        $this->loginAttemptTracker->recordSuccess($normalizedIdentifier, null, $request->ip(), $request->userAgent());

        return $this->issueLoginPayload($user, $request);
    }

    private function issueLoginPayload(User $user, Request $request): array
    {
        $user->loadMissing(['role.permissions', 'school', 'assignedSchools']);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return [
            'token' => $user->createToken('api')->plainTextToken,
            'user' => $user->fresh(['role.permissions', 'school', 'assignedSchools']),
            'permissions' => $this->permissionResolver->resolveForUser($user),
        ];
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        return User::query()
            ->with(['role.permissions', 'school', 'assignedSchools'])
            ->where(function ($query) use ($identifier): void {
                $query
                    ->whereRaw('lower(email) = ?', [mb_strtolower($identifier)])
                    ->orWhereRaw('lower(username) = ?', [mb_strtolower($identifier)])
                    ->orWhere('phone', $identifier);
            })
            ->first();
    }

    private function canAuthenticate(?User $user, string $password): bool
    {
        return $user !== null
            && $user->status === 'active'
            && Hash::check($password, $user->password_hash);
    }

    private function isUserLinkedToSchool(User $user, int $schoolId): bool
    {
        return DB::table('user_school_assignments')
            ->where('user_id', $user->id)
            ->where('school_id', $schoolId)
            ->exists();
    }

    private function normalizeIdentifier(string $identifier): string
    {
        return trim($identifier);
    }

    private function normalizeSchoolCode(string $schoolCode): string
    {
        return mb_strtoupper(trim($schoolCode));
    }
}
