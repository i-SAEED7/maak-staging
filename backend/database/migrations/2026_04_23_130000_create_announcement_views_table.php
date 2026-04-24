<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_views', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('viewer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('viewer_role', 50)->nullable();
            $table->timestamp('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_views');
    }
};
