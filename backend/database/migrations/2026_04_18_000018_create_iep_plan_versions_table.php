<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iep_plan_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('iep_plan_id')->constrained('iep_plans')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->json('content_json');
            $table->text('change_summary')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['iep_plan_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_plan_versions');
    }
};
