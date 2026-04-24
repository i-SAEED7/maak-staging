<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iep_plan_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('iep_plan_id')->constrained('iep_plans')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('author_user_id')->constrained('users');
            $table->string('target_section', 100)->nullable();
            $table->text('comment_text');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_plan_comments');
    }
};
