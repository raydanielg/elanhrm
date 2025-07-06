<?php

use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('modules', 'is_superadmin')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->boolean('is_superadmin')->default(0);
            });
        }


        $checkFirstExists = Module::withoutGlobalScopes()->where('module_name', 'packages')->exists();

        if (!$checkFirstExists) {
            $modules = Module::SUPERADMIN_MODULE_LIST;

            foreach ($modules as $module) {
                $moduleData = Module::withoutGlobalScopes()->where('module_name', $module['module_name'])->first() ?: new Module();

                $moduleData->module_name = $module['module_name'];
                $moduleData->description = ($module['description'] ?? null);
                $moduleData->is_superadmin = (isset($module['is_superadmin']) ? 1 : 0); // Check superadmin permissions
                $moduleData->save();

                // Run for every permissions
                foreach ($module['permissions'] as $permission) {
                    $permission['module_id'] = $moduleData->id;
                    $permission['display_name'] = $permission['display_name'] ?? ucwords(str_replace('_', ' ', $permission['name']));

                    Permission::updateOrCreate(
                        ['module_id' => $permission['module_id'], 'name' => $permission['name']],
                        $permission
                    );

                }
            }

            $superadminRole = Role::withoutGlobalScope(\App\Scopes\CompanyScope::class)->whereNull('company_id')->where('name', 'superadmin')->first();

            if (is_null($superadminRole)) {
                // Add superadmin manager role
                $role = new Role();
                $role->company_id = null;
                $role->name = 'superadmin';
                $role->display_name = 'Superadmin';
                $role->save();

                $permissions = Permission::whereHas('module', function ($query) {
                    $query->withoutGlobalScopes()->where('is_superadmin', '1');
                })->get();

                $permissionRole = [];
                $userPermission = [];

                $superadmins = User::withoutGlobalScopes()->whereNull('company_id')->where('is_superadmin', 1)->get();

                foreach ($permissions as $permission) {
                    $permissionRole[] = [
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                        'permission_type_id' => 4,
                    ];
                }

                foreach ($superadmins as $superadmin) {
                    $superadmin->roles()->attach($role->id); // id only

                    foreach ($permissions as $permission) {

                        $userPermission [] = [
                            'user_id' => $superadmin->id,
                            'permission_id' => $permission->id,
                            'permission_type_id' => 4,
                        ];
                    }
                }

                foreach (array_chunk($permissionRole, 200) as $permissionRoleChunk) {
                    PermissionRole::insert($permissionRoleChunk);
                }

                foreach (array_chunk($userPermission, 200) as $userPermissionChunk) {
                    UserPermission::insert($userPermissionChunk);
                }
            }
        }





        session()->forget('sidebar_superadmin_perms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('is_superadmin');
        });
    }

};
