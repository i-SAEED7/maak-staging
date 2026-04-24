<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iep_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('disability_category_id')->nullable()->constrained('disability_categories')->nullOnDelete();
            $table->foreignId('education_program_id')->nullable()->constrained('education_programs')->nullOnDelete();
            $table->string('title');
            $table->json('template_schema');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_templates');
    }
};
