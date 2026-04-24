<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('full_name');
            $table->text('national_id_encrypted')->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 30)->nullable()->unique();
            $table->string('password_hash');
            $table->string('status', 20)->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('locale', 10)->default('ar');
            $table->boolean('must_change_password')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->unsignedBigInteger('profile_photo_file_id')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
