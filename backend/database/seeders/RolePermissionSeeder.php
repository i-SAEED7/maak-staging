<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seed-data/mappings/role-permissions.json');
        $mapping = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        DB::table('role_permissions')->delete();

        foreach ($mapping as $roleName => $permissions) {
            $role = Role::query()->where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            if ($permissions === ['*']) {
                $permissions = Permission::query()->pluck('key')->all();
            }

            $permissionIds = Permission::query()->whereIn('key', $permissions)->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'granted_at' => now(),
                ]);
            }
        }
    }
}
