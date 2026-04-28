<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountApprovalRequest;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AccountApprovalService
{
    private const ALLOWED_ACCOUNT_TYPES = ['parent', 'teacher', 'principal'];

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = max(10, min((int) ($filters['per_page'] ?? 20), 100));
        $search = trim((string) ($filters['search'] ?? ''));

        return AccountApprovalRequest::query()
            ->with(['school', 'createdUser', 'approvedBy'])
            ->when($filters['status'] ?? 'pending', fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('school', fn (Builder $schoolQuery) => $schoolQuery->where('name_ar', 'like', "%{$search}%"));
                });
            })
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): AccountApprovalRequest
    {
        $school = School::query()->whereKey($data['school_id'])->first();

        if (! $school || $school->status !== 'active') {
            throw ValidationException::withMessages([
                'school_id' => ['المدرسة المحددة غير متاحة للتسجيل.'],
            ]);
        }

        if ($school->stage !== null && $school->stage !== $data['stage']) {
            throw ValidationException::withMessages([
                'school_id' => ['المدرسة المحددة لا تتبع المرحلة المختارة.'],
            ]);
        }

        $approval = AccountApprovalRequest::query()->create([
            'uuid' => (string) Str::uuid(),
            'first_name' => $data['first_name'],
            'second_name' => $data['second_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'account_type' => $data['account_type'],
            'password_hash' => Hash::make($data['password']),
            'stage' => $data['stage'],
            'school_id' => $school->id,
            'status' => 'pending',
        ]);

        return $approval->load(['school', 'createdUser', 'approvedBy']);
    }

    public function update(AccountApprovalRequest $approval, array $data): AccountApprovalRequest
    {
        if ($approval->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['لا يمكن تعديل طلب تمت معالجته.'],
            ]);
        }

        $payload = collect($data)
            ->only(['first_name', 'second_name', 'last_name', 'email', 'phone', 'account_type', 'stage', 'school_id'])
            ->all();

        if (! empty($data['password'])) {
            $payload['password_hash'] = Hash::make($data['password']);
        }

        if (isset($payload['school_id']) || isset($payload['stage'])) {
            $school = School::query()->whereKey($payload['school_id'] ?? $approval->school_id)->first();
            $stage = $payload['stage'] ?? $approval->stage;

            if (! $school || ($school->stage !== null && $school->stage !== $stage)) {
                throw ValidationException::withMessages([
                    'school_id' => ['المدرسة المحددة لا تتبع المرحلة المختارة.'],
                ]);
            }
        }

        $approval->update($payload);

        return $approval->refresh()->load(['school', 'createdUser', 'approvedBy']);
    }

    public function approve(AccountApprovalRequest $approval, User $approver): AccountApprovalRequest
    {
        if ($approval->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['هذا الطلب تمت معالجته مسبقًا.'],
            ]);
        }

        if (User::query()->where('email', $approval->email)->orWhere('phone', $approval->phone)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['يوجد حساب مسجل بنفس البريد أو رقم الجوال.'],
            ]);
        }

        if (! in_array((string) $approval->account_type, self::ALLOWED_ACCOUNT_TYPES, true)) {
            throw ValidationException::withMessages([
                'account_type' => ['يجب اختيار نوع الحساب قبل الاعتماد.'],
            ]);
        }

        $role = Role::query()->where('name', $approval->account_type)->first();

        if (! $role) {
            throw ValidationException::withMessages([
                'role' => ['تعذر تحديد الدور المرتبط بنوع الحساب المختار.'],
            ]);
        }

        DB::transaction(function () use ($approval, $approver, $role): void {
            $user = User::query()->create([
                'uuid' => (string) Str::uuid(),
                'role_id' => $role->id,
                'school_id' => $approval->school_id,
                'full_name' => $approval->fullName(),
                'username' => $this->generateUsername($approval->email, $approval->phone),
                'email' => $approval->email,
                'phone' => $approval->phone,
                'password_hash' => $approval->password_hash,
                'status' => 'active',
                'is_central' => false,
                'must_change_password' => false,
                'locale' => 'ar',
                'metadata' => [
                    'source' => 'account_approval_request',
                    'account_approval_request_id' => $approval->id,
                ],
            ]);

            DB::table('user_school_assignments')->updateOrInsert([
                'user_id' => $user->id,
                'school_id' => $approval->school_id,
            ], [
                'assignment_type' => 'member',
                'created_at' => now(),
            ]);

            $approval->update([
                'status' => 'approved',
                'approved_by_user_id' => $approver->id,
                'created_user_id' => $user->id,
                'approved_at' => now(),
            ]);
        });

        return $approval->refresh()->load(['school', 'createdUser', 'approvedBy']);
    }

    private function generateUsername(string $email, string $phone): string
    {
        $base = Str::of($email)->before('@')->lower()->replaceMatches('/[^a-z0-9._-]/', '');

        if ((string) $base === '') {
            $base = Str::of($phone)->replaceMatches('/[^0-9]/', '');
        }

        $base = Str::limit(trim((string) $base) ?: 'user', 55, '');
        $candidate = (string) $base;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = Str::limit((string) $base, 55 - strlen((string) $suffix), '') . '_' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}
