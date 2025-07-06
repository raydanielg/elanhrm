<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\Role\StoreRole as RoleStoreRole;
use App\Models\GlobalSetting;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\PermissionType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use App\Scopes\CompanyScope;
use App\Scopes\SuperAdminModuleScope;
use Illuminate\Http\Request;

class SuperadminRolePermissionController extends AccountBaseController
{

    protected array $permissionTypes = [
        'added' => 1,
        'owned' => 2,
        'both' => 3,
        'all' => 4,
        'none' => 5
    ];

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.rolesPermission';
        $this->activeSettingMenu = 'superadmin_role_permissions';
        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_permission_settings'));

            return $next($request);
        });
    }

    public function index()
    {
        abort_403(user()->permission('manage_superadmin_permission_settings') != 'all');

        $this->roles = Role::withCount(['users' => function ($q) {
            $q->withoutGlobalScopes([CompanyScope::class]);
        }])
            ->withoutGlobalScopes([CompanyScope::class])
            ->orderBy('id', 'asc')
            ->whereNull('company_id')
            ->get();

        $this->totalPermissions = Permission::select('permissions.*')->join('modules', 'modules.id', 'permissions.module_id')->where('modules.is_superadmin', 1)->count();

        return view('super-admin.role-permissions.index', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        abort_403(user()->permission('manage_superadmin_permission_settings') != 'all');

        $this->roles = Role::withCount(['unsyncedUsers' => function ($q) {
            $q->withoutGlobalScopes([CompanyScope::class]);
        }])->with(['roleuser' => function ($q) {
            $q->withoutGlobalScopes([CompanyScope::class]);
        }])->withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->get();

        return view('super-admin.role-permissions.ajax.create', $this->data);
    }

    public function store(Request $request)
    {
        abort_403(user()->permission('manage_superadmin_permission_settings') != 'all');

        $permissionType = $request->permissionType;

        abort_if($permissionType == '', 404);

        $roleId = $request->roleId;
        $permissionId = $request->permissionId;

        $role = Role::with('users', 'users.role')->withoutGlobalScopes([CompanyScope::class])->findOrFail($roleId);

        // Update role's permission
        $permissionRole = PermissionRole::where('permission_id', $permissionId)
            ->where('role_id', $roleId)
            ->first();

        if ($permissionRole) {
            $permissionRole = PermissionRole::where('permission_id', $permissionId)
                ->where('role_id', $roleId)
                ->update(['permission_type_id' => $permissionType]);

        }
        else {
            $permissionRole = new PermissionRole();
            $permissionRole->permission_id = $permissionId;
            $permissionRole->role_id = $roleId;
            $permissionRole->permission_type_id = $permissionType;
            $permissionRole->save();

        }

        // Update user permission with the role
        foreach ($role->users as $roleuser) {
            $userPermission = UserPermission::where('user_permissions.permission_id', $permissionId)
                ->leftJoin('users', 'users.id', '=', 'user_permissions.user_id')
                ->where('user_permissions.user_id', $roleuser->id)
                ->select('users.customised_permissions', 'user_permissions.*')
                ->firstOrNew();

            if ($userPermission->customised_permissions == 0) {
                $userPermission->permission_id = $permissionId;
                $userPermission->user_id = $roleuser->id;
                $userPermission->permission_type_id = $permissionType;
                $userPermission->save();
            }

        }

        return Reply::dataOnly(['status' => 'success']);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions()
    {
        $roleId = request('roleId');

        $this->role = Role::with('permissions')->withoutGlobalScopes([CompanyScope::class])->where('name', '<>', 'superadmin')->findOrFail($roleId);

        $this->modulesData = Module::with('permissions')->withoutGlobalScopes([SuperAdminModuleScope::class])->where('is_superadmin', 1)->withCount('customPermissions')->groupBy('modules.id')->get();

        $html = view('super-admin.role-permissions.ajax.permissions', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserPermissions($roleId, $userId)
    {
        $rolePermissions = PermissionRole::where('role_id', $roleId)->get();

        foreach ($rolePermissions as $key => $value) {
            UserPermission::where('permission_id', $value->permission_id)
                ->where('user_id', $userId)
                ->update(['permission_type_id' => $value->permission_type_id]);
        }

        return Reply::dataOnly(['status' => 'success']);
    }

    public function storeRole(RoleStoreRole $request)
    {
        abort_403(user()->permission('manage_superadmin_permission_settings') != 'all');

        $role = new Role();
        $role->name = $request->name;
        $role->display_name = $request->name;
        $role->save();

        if ($request->import_from_role != '') {
            $importRolePermissions = PermissionRole::where('role_id', $request->import_from_role)->get();

            if (count($importRolePermissions) == 0) {
                return Reply::error(__('messages.noRoleFound'));
            }

            foreach ($importRolePermissions as $perm) {
                $perm->replicate()->fill([
                    'role_id' => $role->id
                ])->save();
            }

        }
        else {
            $allPermissions = Permission::select('permissions.*')->join('modules', 'modules.id', 'permissions.module_id')->where('modules.is_superadmin', 1)->get();
            $role->perms()->sync([]);
            $role->attachPermissions($allPermissions);
        }

        return Reply::success(__('messages.recordSaved'));
    }

    public function deleteRole(Request $request)
    {
        Role::whereId($request->roleId)->withoutGlobalScope(CompanyScope::class)->delete();

        return Reply::dataOnly(['status' => 'success']);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function customPermissions(Request $request)
    {
        $moduleId = $request->moduleId;
        $roleId = request('roleId');
        $this->role = Role::with('permissions')->withoutGlobalScopes([CompanyScope::class])->findOrFail($roleId);
        $this->modulesData = Module::with('customPermissions')->withoutGlobalScopes([SuperAdminModuleScope::class])->where('is_superadmin', 1)->findOrFail($moduleId);

        $html = view('super-admin.role-permissions.ajax.custom_permissions', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

    public function resetPermissions()
    {
        return Reply::error(__('messages.permissionDenied'));

    }

    public function update(Request $request, $id)
    {
        $role = Role::where('id', '<>', $id)->whereNull('company_id')->where('name', $request->role_name)->first();

        if (!is_null($role)) {
            return Reply::error(__('superadmin.roleAlreadyExist'));
        }

        Role::where('id', $id)->withoutGlobalScopes([CompanyScope::class])->update(['display_name' => $request->role_name]);
    }

    public function addMissingSuperAdminPermission()
    {
        $superAdminRole = Role::where('name', 'superadmin')->withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->first();

        if ($superAdminRole) {
            $superAdminPermission = PermissionRole::where('role_id', $superAdminRole->id)->pluck('permission_id')->toArray();

            $allTypePermisison = PermissionType::where('name', 'all')->first();
            $missingPermissions = Permission::select('permissions.*')->join('modules', 'modules.id', 'permissions.module_id')->where('modules.is_superadmin', 1)->select('id')->whereNotIn('id', $superAdminPermission)->get();

            $data = [];

            foreach ($missingPermissions as $permission) {
                $data[] = [
                    'permission_id' => $permission->id,
                    'role_id' => $superAdminRole->id,
                    'permission_type_id' => $allTypePermisison->id,
                ];
            }

            foreach (array_chunk($data, 100) as $item) {
                PermissionRole::insert($item);
            }

            if (count($missingPermissions) > 0) {
                $this->addMissingAdminUserPermission($superAdminRole->id);
            }

        }

    }

    public function addMissingAdminUserPermission($roleId)
    {
        $role = Role::withCount('permissions')->withoutGlobalScope(CompanyScope::class)->findOrFail($roleId);
        $users = $role->users;

        foreach ($users as $user) {
            $user->assignUserRolePermission($roleId);
        }
    }

    public function addMissingUserPermission($roleId)
    {
        $role = Role::withCount('permissions')->withoutGlobalScope(CompanyScope::class)->findOrFail($roleId);
        $users = $role->users;

        foreach ($users as $user) {
            $userRole = $user->roles->pluck('name')->toArray();

            if (!in_array('superadmin', $userRole)) {
                $user->assignUserRolePermission($roleId);
            }
        }
    }

    public function rolePermissionInsert($allPermissions, $roleId, $permissionType = 'none')
    {
        $data = [];

        foreach ($allPermissions as $permission) {
            $data[] = [
                'permission_id' => $permission->id,
                'role_id' => $roleId,
                'permission_type_id' => $this->permissionTypes[$permissionType],
            ];
        }

        foreach (array_chunk($data, 100) as $item) {
            PermissionRole::insert($item);
        }

    }

    public function permissionRole($allPermissions, $type)
    {
        $role = Role::withoutGlobalScope(CompanyScope::class)->with('roleuser', 'roleuser.user.roles')
            ->where('name', $type)
            ->whereNull('company_id')
            ->first();

        PermissionRole::where('role_id', $role->id)->delete();

        $this->rolePermissionInsert($allPermissions, $role->id);

        $permissionArray = [];

        $permissionArrayKeys = array_keys($permissionArray);

        $permissions = Permission::select('permissions.*')->whereIn('name', $permissionArrayKeys)->join('modules', 'modules.id', 'permissions.module_id')->where('modules.is_superadmin', 1)->get();

        PermissionRole::whereIn('permission_id', $permissions->pluck('id')->toArray())
            ->where('role_id', $role->id)
            ->delete();

        $updatePermissionArray = [];

        foreach ($permissions as $permission) {
            $updatePermissionArray[] = ['permission_id' => $permission->id, 'role_id' => $role->id, 'permission_type_id' => $permissionArray[$permission->name]];
        }

        PermissionRole::insert($updatePermissionArray);
    }

}
