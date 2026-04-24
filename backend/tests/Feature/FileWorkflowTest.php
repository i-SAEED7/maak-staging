<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoBrowserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class FileWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_issue_temporary_link_and_soft_delete_file(): void
    {
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);
        $this->seed(DemoBrowserSeeder::class);

        $user = User::query()->where('email', 'superadmin@maak.local')->firstOrFail();
        $student = Student::withoutGlobalScopes()->firstOrFail();

        Sanctum::actingAs($user);

        $uploadResponse = $this
            ->withHeaders([
                'Accept' => 'application/json',
                'X-School-Id' => '1',
            ])
            ->post('/api/v1/files', [
                'file' => UploadedFile::fake()->createWithContent('notes.txt', 'file workflow smoke test'),
                'category' => 'general',
                'visibility' => 'school',
                'related_type' => Student::class,
                'related_id' => $student->id,
                'is_sensitive' => '0',
            ]);

        $uploadResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.original_name', 'notes.txt');

        $fileId = (int) $uploadResponse->json('data.id');
        $storagePath = (string) $uploadResponse->json('data.storage_path');

        Storage::disk('local')->assertExists($storagePath);

        $this
            ->withHeaders([
                'Accept' => 'application/json',
                'X-School-Id' => '1',
            ])
            ->getJson('/api/v1/files')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this
            ->withHeaders([
                'Accept' => 'application/json',
                'X-School-Id' => '1',
            ])
            ->getJson("/api/v1/files/{$fileId}")
            ->assertOk()
            ->assertJsonPath('data.id', $fileId);

        $temporaryLinkResponse = $this
            ->withHeaders([
                'Accept' => 'application/json',
                'X-School-Id' => '1',
            ])
            ->postJson("/api/v1/files/{$fileId}/temporary-link", [
                'expires_in_minutes' => 15,
            ]);

        $temporaryLinkResponse
            ->assertOk()
            ->assertJsonPath('success', true);

        $downloadPath = (string) parse_url(
            (string) $temporaryLinkResponse->json('data.temporary_link.url'),
            PHP_URL_PATH,
        );

        $downloadResponse = $this->get($downloadPath);

        $downloadResponse->assertOk();
        $this->assertStringStartsWith(
            'text/plain',
            (string) $downloadResponse->headers->get('content-type'),
        );

        $this->get($downloadPath)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['token']);

        $this
            ->withHeaders([
                'Accept' => 'application/json',
                'X-School-Id' => '1',
            ])
            ->deleteJson("/api/v1/files/{$fileId}")
            ->assertOk()
            ->assertJsonPath('data.id', $fileId);
    }
}
