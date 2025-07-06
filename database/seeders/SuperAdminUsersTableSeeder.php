<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserAuth;
use App\Models\Permission;
use App\Scopes\CompanyScope;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;

class SuperAdminUsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $faker = \Faker\Factory::create();

        $superadmin = new User();
        $superadmin->name = $faker->name;
        $superadmin->email = 'superadmin@example.com';
        $superadmin->is_superadmin = true;
        $superadmin->save();

        $userAuth = UserAuth::create(['email' => $superadmin->email, 'password' => bcrypt('123456'), 'email_verified_at' => now()]);
        $superadmin->user_auth_id = $userAuth->id;
        $superadmin->saveQuietly();

        self::superadminRolePermissionAttach($superadmin);
    }

    public static function superadminRolePermissionAttach(User $user)
    {
        $superadminRole = Role::withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->where('name', 'superadmin')->first();

        $user->roles()->attach($superadminRole->id); // id only

        $permissions = Permission::select('permissions.*')->whereHas('module', function ($query) {
            $query->withoutGlobalScopes()->where('is_superadmin', '1');
        })->get();

        $userPermission = [];

        foreach ($permissions as $permission) {
            $userPermission [] = [
                'user_id' => $user->id,
                'permission_id' => $permission->id,
                'permission_type_id' => 4,
            ];
        }

        foreach (array_chunk($userPermission, 200) as $userPermissionChunk) {
            UserPermission::insert($userPermissionChunk);
        }
    }

}
