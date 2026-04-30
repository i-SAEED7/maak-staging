<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iep_goal_banks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('disability_category_id')
                ->constrained('disability_categories')
                ->cascadeOnDelete();

            $table->string('domain', 100)
                ->comment('المجال التعليمي: أكاديمي، سلوكي، اجتماعي، تواصلي، حركي، استقلالي');

            $table->text('goal_text')
                ->comment('نص الهدف التعليمي');

            $table->jsonb('strategies')
                ->nullable()
                ->comment('قائمة الاستراتيجيات المقترحة');

            $table->jsonb('suggested_criteria')
                ->nullable()
                ->comment('معايير التقييم المقترحة');

            $table->unsignedTinyInteger('grade_level_min')->default(1)
                ->comment('أدنى صف دراسي مناسب');

            $table->unsignedTinyInteger('grade_level_max')->default(12)
                ->comment('أعلى صف دراسي مناسب');

            $table->boolean('is_active')->default(true);

            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->jsonb('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast filtering
            $table->index(['disability_category_id', 'domain', 'is_active'], 'goal_bank_filter_idx');
            $table->index(['grade_level_min', 'grade_level_max'], 'goal_bank_grade_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_goal_banks');
    }
};
