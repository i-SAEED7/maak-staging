<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('effect', 16);
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
            $table->index(['user_id', 'effect']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
