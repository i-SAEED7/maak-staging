<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('thread_key', 100);
            $table->unsignedBigInteger('sender_user_id');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->unsignedBigInteger('parent_message_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
