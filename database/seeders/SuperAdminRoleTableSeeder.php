<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Scopes\CompanyScope;
use App\Models\PermissionRole;
use Illuminate\Database\Seeder;

class SuperAdminRoleTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        $superadminRole = Role::withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->where('name', 'superadmin')->first();

        if (is_null($superadminRole)) {
            $role = new Role();
            $role->name = 'superadmin';
            $role->display_name = 'Superadmin';
            $role->save();

            $permissions = Permission::whereHas('module', function ($query) {
                $query->withoutGlobalScopes()->where('is_superadmin', '1');
            })->get();

            $permissionRole = [];

            foreach ($permissions as $permission) {
                $permissionRole[] = [
                    'permission_id' => $permission->id,
                    'role_id' => $role->id,
                    'permission_type_id' => 4,
                ];
            }

            foreach (array_chunk($permissionRole, 200) as $permissionRoleChunk) {
                PermissionRole::insert($permissionRoleChunk);
            }
        }

    }

}
