<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seed-data/roles.json');
        $roles = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'display_name_ar' => $role['display_name_ar'],
                    'description' => $role['description'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
