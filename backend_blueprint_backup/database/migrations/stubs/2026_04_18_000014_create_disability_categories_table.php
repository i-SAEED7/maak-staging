<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disability_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name_ar', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disability_categories');
    }
};
