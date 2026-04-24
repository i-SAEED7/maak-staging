<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iep_plan_goals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('iep_plan_id')->constrained('iep_plans')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('domain', 100);
            $table->text('goal_text');
            $table->text('measurement_method')->nullable();
            $table->string('baseline_value', 100)->nullable();
            $table->string('target_value', 100)->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_plan_goals');
    }
};
