<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable();
            $table->string('related_type', 100)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('category', 30);
            $table->string('original_name');
            $table->string('storage_name')->unique();
            $table->string('storage_disk', 50);
            $table->text('storage_path');
            $table->string('mime_type', 150);
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->string('checksum_sha256', 64)->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->string('visibility', 20)->default('private');
            $table->timestamp('uploaded_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
