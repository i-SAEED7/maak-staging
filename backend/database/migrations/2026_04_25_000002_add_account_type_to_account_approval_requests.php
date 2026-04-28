<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('account_approval_requests') || Schema::hasColumn('account_approval_requests', 'account_type')) {
            return;
        }

        Schema::table('account_approval_requests', function (Blueprint $table): void {
            $table->string('account_type', 30)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('account_approval_requests') || ! Schema::hasColumn('account_approval_requests', 'account_type')) {
            return;
        }

        Schema::table('account_approval_requests', function (Blueprint $table): void {
            $table->dropColumn('account_type');
        });
    }
};
