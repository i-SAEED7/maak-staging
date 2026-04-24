<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supervisor_visits', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedBigInteger('supervisor_user_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->date('visit_date');
            $table->string('visit_status', 20)->default('scheduled');
            $table->text('agenda')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_visits');
    }
};
