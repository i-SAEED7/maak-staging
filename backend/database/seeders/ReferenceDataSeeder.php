<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $disabilities = json_decode(File::get(database_path('seed-data/disability-categories.json')), true, 512, JSON_THROW_ON_ERROR);
        $programs = json_decode(File::get(database_path('seed-data/education-programs.json')), true, 512, JSON_THROW_ON_ERROR);
        $legacyProgramCodes = ['inclusion', 'resource_room', 'special_class', 'special_institute', 'early_intervention'];

        foreach ($disabilities as $item) {
            DB::table('disability_categories')->updateOrInsert(
                ['code' => $item['code']],
                [
                    'name_ar' => $item['name_ar'],
                    'description' => $item['description'] ?? null,
                    'is_active' => $item['is_active'] ?? true,
                ],
            );
        }

        foreach ($programs as $item) {
            DB::table('education_programs')->updateOrInsert(
                ['code' => $item['code']],
                [
                    'name_ar' => $item['name_ar'],
                    'description' => $item['description'] ?? null,
                    'is_active' => $item['is_active'] ?? true,
                ],
            );
        }

        DB::table('education_programs')
            ->whereIn('code', $legacyProgramCodes)
            ->update(['is_active' => false]);

        $yasirProgramId = DB::table('education_programs')
            ->where('code', 'yasir_learning')
            ->value('id');

        if ($yasirProgramId !== null) {
            DB::table('students')
                ->whereIn('education_program_id', function ($query) use ($legacyProgramCodes): void {
                    $query->select('id')
                        ->from('education_programs')
                        ->whereIn('code', $legacyProgramCodes);
                })
                ->update(['education_program_id' => $yasirProgramId]);
        }
    }
}
