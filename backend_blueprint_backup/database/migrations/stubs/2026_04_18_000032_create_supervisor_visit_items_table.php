<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supervisor_visit_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('visit_id')->constrained('supervisor_visits')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('criterion_key', 100);
            $table->string('criterion_label');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_visit_items');
    }
};
