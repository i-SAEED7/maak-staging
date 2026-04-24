<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoBrowserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_central_user_can_login_through_central_route(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $this->postJson('/api/auth/central-login', [
            'identifier' => 'superadmin',
            'password' => 'Password@123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'superadmin')
            ->assertJsonPath('data.user.role', 'super_admin')
            ->assertJsonPath('data.user.is_central', true);
    }

    public function test_school_user_can_login_through_school_route_with_school_code(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $this->postJson('/api/auth/school-login', [
            'identifier' => 'teacher',
            'password' => 'Password@123',
            'school_code' => 'JED-P-00001',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'teacher')
            ->assertJsonPath('data.user.role', 'teacher')
            ->assertJsonPath('data.user.is_central', false)
            ->assertJsonPath('data.user.school.school_code', 'JED-P-00001')
            ->assertJsonPath('data.user.school.slug', 'jed-p-00001');
    }

    public function test_school_user_cannot_login_through_central_route(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $this->postJson('/api/auth/central-login', [
            'identifier' => 'teacher',
            'password' => 'Password@123',
        ])
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'الحساب غير مصرح له بالدخول من هذا المسار');
    }

    public function test_central_user_cannot_login_through_school_route(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $this->postJson('/api/auth/school-login', [
            'identifier' => 'superadmin',
            'password' => 'Password@123',
            'school_code' => 'JED-P-00001',
        ])
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'بيانات الدخول غير صحيحة أو غير مصرح بها');
    }

    public function test_login_is_temporarily_locked_after_repeated_failed_attempts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/central-login', [
                'identifier' => 'superadmin',
                'password' => 'WrongPassword@123',
            ])->assertStatus(422);
        }

        $this->postJson('/api/auth/central-login', [
            'identifier' => 'superadmin',
            'password' => 'WrongPassword@123',
        ])
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'تم إيقاف المحاولات مؤقتًا. حاول مرة أخرى لاحقًا.');
    }
}
