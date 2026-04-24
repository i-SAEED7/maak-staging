<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->string('gender', 30)->default('غير محدد')->after('program_type');
            $table->index(['stage', 'gender']);
        });

        DB::table('schools')
            ->whereNull('gender')
            ->update(['gender' => 'غير محدد']);
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropIndex(['stage', 'gender']);
            $table->dropColumn('gender');
        });
    }
};
