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
            $table->string('stage', 100)->nullable()->after('ministry_code');
            $table->string('program_type', 100)->nullable()->after('stage');
            $table->decimal('location_lat', 10, 7)->nullable()->after('address');
            $table->decimal('location_lng', 10, 7)->nullable()->after('location_lat');
            $table->unsignedBigInteger('principal_id')->nullable()->after('principal_user_id');
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('principal_id');
            $table->index(['status', 'program_type']);
            $table->index(['city', 'stage']);
        });

        $schools = DB::table('schools')
            ->select('id', 'latitude', 'longitude', 'principal_user_id')
            ->get();

        foreach ($schools as $school) {
            DB::table('schools')
                ->where('id', $school->id)
                ->update([
                    'location_lat' => $school->latitude,
                    'location_lng' => $school->longitude,
                    'principal_id' => $school->principal_user_id,
                ]);
        }

        $supervisorAssignments = DB::table('user_school_assignments')
            ->where('assignment_type', 'supervising')
            ->orderBy('id')
            ->get()
            ->unique('school_id');

        foreach ($supervisorAssignments as $assignment) {
            DB::table('schools')
                ->where('id', $assignment->school_id)
                ->update([
                    'supervisor_id' => $assignment->user_id,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropIndex(['status', 'program_type']);
            $table->dropIndex(['city', 'stage']);
            $table->dropColumn([
                'stage',
                'program_type',
                'location_lat',
                'location_lng',
                'principal_id',
                'supervisor_id',
            ]);
        });
    }
};
