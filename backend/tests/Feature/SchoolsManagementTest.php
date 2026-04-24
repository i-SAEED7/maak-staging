<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoBrowserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class SchoolsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_list_create_update_and_deactivate_school(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $user = User::query()->where('email', 'superadmin@maak.local')->firstOrFail();
        $principal = User::query()->where('email', 'principal@maak.local')->firstOrFail();
        $supervisor = User::query()->where('email', 'supervisor@maak.local')->firstOrFail();

        Sanctum::actingAs($user);

        $this->withHeaders(['Accept' => 'application/json'])
            ->getJson('/api/v1/schools?per_page=10')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.page', 1);

        $createResponse = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson('/api/v1/schools', [
                'name' => 'مدرسة البيان لذوي الإعاقة',
                'stage' => 'متوسط',
                'program_type' => 'يسير التعليمي',
                'location_lat' => 21.543333,
                'location_lng' => 39.172779,
                'principal_id' => $principal->id,
                'supervisor_id' => $supervisor->id,
            ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'مدرسة البيان لذوي الإعاقة')
            ->assertJsonPath('data.principal_id', $principal->id)
            ->assertJsonPath('data.supervisor_id', $supervisor->id);

        $schoolId = (int) $createResponse->json('data.id');
        $this->assertMatchesRegularExpression('/^JED-(S|I|P)-\d{5}$/', (string) $createResponse->json('data.official_code'));

        $this->withHeaders(['Accept' => 'application/json'])
            ->putJson("/api/v1/schools/{$schoolId}", [
                'name' => 'مدرسة البيان لذوي الإعاقة - محدثة',
                'stage' => 'ثانوي',
                'program_type' => 'فرط حركة وتشتت انتباه',
                'location_lat' => 21.543333,
                'location_lng' => 39.172779,
                'principal_id' => $principal->id,
                'supervisor_id' => $supervisor->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.stage', 'ثانوي')
            ->assertJsonPath('data.program_type', 'فرط حركة وتشتت انتباه');

        $this->withHeaders(['Accept' => 'application/json'])
            ->deleteJson("/api/v1/schools/{$schoolId}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('schools', [
            'id' => $schoolId,
            'status' => 'inactive',
            'principal_id' => $principal->id,
            'supervisor_id' => $supervisor->id,
        ]);
    }

    public function test_principal_cannot_create_school(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $principal = User::query()->where('email', 'principal@maak.local')->firstOrFail();
        $supervisor = User::query()->where('email', 'supervisor@maak.local')->firstOrFail();

        Sanctum::actingAs($principal);

        $this->withHeaders(['Accept' => 'application/json'])
            ->postJson('/api/v1/schools', [
                'name' => 'مدرسة غير مصرح بها',
                'stage' => 'ابتدائي',
                'program_type' => 'يسير التعليمي',
                'location_lat' => 24.713552,
                'location_lng' => 46.675296,
                'principal_id' => $principal->id,
                'supervisor_id' => $supervisor->id,
            ])
            ->assertForbidden();
    }
}
