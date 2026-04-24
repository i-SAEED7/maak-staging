<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('education_program_id')->nullable();
            $table->unsignedBigInteger('disability_category_id')->nullable();
            $table->unsignedBigInteger('primary_teacher_user_id')->nullable();
            $table->string('first_name', 100);
            $table->string('father_name', 100)->nullable();
            $table->string('grandfather_name', 100)->nullable();
            $table->string('family_name', 100);
            $table->string('full_name');
            $table->text('national_id_encrypted')->nullable();
            $table->string('student_number', 50)->nullable();
            $table->string('gender', 10);
            $table->date('birth_date')->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->string('classroom', 50)->nullable();
            $table->string('enrollment_status', 20)->default('active');
            $table->json('medical_notes')->nullable();
            $table->json('social_notes')->nullable();
            $table->text('transportation_notes')->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
