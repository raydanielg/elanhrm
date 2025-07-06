<?php

use App\Models\Role;
use App\Models\Permission;
use App\Scopes\CompanyScope;
use App\Models\PermissionRole;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SAAS
        $this->resetSuperAdminRolePermission();

        \App\Models\User::withoutGlobalScopes()->where('customised_permissions', 0)->update(['permission_sync' => 0]);

        Artisan::call('sync-user-permissions', ['all' => true]);

        Schema::table('expenses', function (Blueprint $table) {
            $table->longtext('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }

    public function resetSuperAdminRolePermission()
    {
        $role = Role::withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->where('name', 'superadmin')->first();

        if ($role) {
            PermissionRole::where('role_id', $role->id)->delete();

            $permissions = Permission::whereHas('module', function ($query) {
                $query->withoutGlobalScopes()->where('is_superadmin', '1');
            })->get();

            // Delete all permission role of superadmin except superadmin
            PermissionRole::where('role_id', '!=', $role->id)->whereIn('permission_id', $permissions->pluck('id'))->delete();

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

};
