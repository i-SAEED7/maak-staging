<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iep_plans', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('teacher_user_id');
            $table->unsignedBigInteger('principal_user_id')->nullable();
            $table->unsignedBigInteger('supervisor_user_id')->nullable();
            $table->unsignedInteger('current_version_number')->default(1);
            $table->string('status', 30)->default('draft');
            $table->string('title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('summary')->nullable();
            $table->text('strengths')->nullable();
            $table->text('needs')->nullable();
            $table->json('accommodations')->nullable();
            $table->unsignedBigInteger('generated_pdf_file_id')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iep_plans');
    }
};
