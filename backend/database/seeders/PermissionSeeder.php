<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seed-data/permissions.json');
        $permissions = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $permission['key']],
                [
                    'display_name_ar' => $permission['display_name_ar'],
                    'module' => $permission['module'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
