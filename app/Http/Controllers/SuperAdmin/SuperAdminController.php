<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\AppSettingController;
use App\Models\Role;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\UserAuth;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use App\DataTables\SuperAdmin\SuperAdminDataTable;
use App\Http\Requests\SuperAdmin\SuperAdmin\StoreRequest;
use App\Http\Requests\SuperAdmin\SuperAdmin\UpdateRequest;
use App\Models\Company;
use App\Models\RoleUser;
use App\Models\UserPermission;
use App\Providers\RouteServiceProvider;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.superAdmin';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SuperAdminDataTable $dataTable)
    {
        $this->viewPermission = user()->permission('view_superadmin');
        abort_403(!($this->viewPermission == 'all'));

        return $dataTable->render('super-admin.super-admin.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_superadmin');
        abort_403(!($this->addPermission == 'all'));

        $this->pageTitle = __('superadmin.superadmin.create');
        $this->view = 'super-admin.super-admin.ajax.create';
        $this->roles = Role::with('users', 'users.role')->withoutGlobalScopes()->whereNull('company_id')->get();

        // SuperAdmin Roles
        $this->userRoles = user()->roles->pluck('name')->toArray();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.super-admin.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->addPermission = user()->permission('add_superadmin');
        abort_403(!($this->addPermission == 'all'));

        DB::beginTransaction();

        $userAuth = UserAuth::createUserAuthCredentials($request->email);

        $superAdmin = new User();
        $superAdmin->name = $request->name;
        $superAdmin->is_superadmin = true;
        $superAdmin->email = $request->email;
        $superAdmin->user_auth_id = $userAuth->id;
        $superAdmin->login = 'enable';
        $superAdmin->status = 'active';

        if ($request->hasFile('image')) {
            Files::deleteFile($superAdmin->image, 'avatar');
            $superAdmin->image = Files::uploadLocalOrS3($request->image, 'avatar', 300);
        }

        $superAdmin->save();

        $userAuth->email_verified_at = now();
        $userAuth->saveQuietly();

        // Roles For New User Start
        $superadminRole = Role::withoutGlobalScopes()->where('id', $request->role)->first();
        $superAdmin->attachRole($superadminRole);
        $superAdmin->assignUserRolePermission($superadminRole->id);
        // Roles For New User End

        DB::commit();

        return Reply::redirect(route('superadmin.superadmin.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_superadmin');
        abort_403(!($this->editPermission == 'all'));

        $this->superAdmin = User::withoutGlobalScope(ActiveScope::class)
            ->where('is_superadmin', 1)
            ->whereNull('company_id')
            ->findOrFail($id);

        $firstSuperAdmin = User::firstSuperAdmin();

        abort_if(($this->superAdmin->id == $firstSuperAdmin->id && $this->user->id != $firstSuperAdmin->id), 403);

        // Roles Select For New User Start
        $this->roles = Role::with('users', 'users.role')->withoutGlobalScopes()->whereNull('company_id')->get();
        $this->userRoles = $this->superAdmin->roles->pluck('name')->toArray();
        // Roles Select For New User End

        $this->pageTitle = __('superadmin.superadmin.edit', ['name' => $this->superAdmin->name]);
        $this->view = 'super-admin.super-admin.ajax.edit';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.super-admin.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_superadmin');
        abort_403(!($this->editPermission == 'all'));

        $superAdmin = User::withoutGlobalScope(ActiveScope::class)->where('is_superadmin', 1)->whereNull('company_id')->findOrFail($id);

        $superAdmin->name = $request->name;
        $superAdmin->email = $request->email;

        $emailCountInCompanies = User::withoutGlobalScopes([ActiveScope::class, CompanyScope::class])
            ->where('email', $superAdmin->email)
            ->count();

        if ($emailCountInCompanies > 1) {
            return Reply::error(__('messages.emailCannotChange'));
        }

        // Change SuperAdmin Role if changed start
        $roleId = request()->role;
        $userRole = Role::withoutGlobalScopes()->where('id', request()->role)->first();
        $superadminRole = $superAdmin->role[0];

        if ($roleId != '' && $userRole->id != $superadminRole->role_id ) {

            $this->changeRole($superAdmin->id, $roleId);
        }

        // Change SuperAdmin Role if changed end

        // Update email in userauth also
        $superAdmin->userAuth()->update(['email' => $request->email]);

        if ($this->user->id != $superAdmin->id) {
            $superAdmin->status = $request->status;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($superAdmin->image, 'avatar');
            $superAdmin->image = Files::uploadLocalOrS3($request->image, 'avatar', 300);
        }

        $superAdmin->save();

        cache()->forget('user_is_active_' . $superAdmin->id);

        return Reply::redirect(route('superadmin.superadmin.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_superadmin');
        abort_403(!($this->deletePermission == 'all'));

        $totalSuperadmin = User::withoutGlobalScopes([CompanyScope::class, ActiveScope::class])
            ->where('is_superadmin', 1)
            ->whereNull('company_id')
            ->count();

        if ($totalSuperadmin == 1) {
            return Reply::error('We require one superadmin for your account. To remove this superadmin, please add an additional superadmin');
        }

        $user = User::withoutGlobalScopes([CompanyScope::class, ActiveScope::class])
            ->where('is_superadmin', 1)
            ->whereNull('company_id')
            ->findOrFail($id);

        $firstSuperAdmin = User::firstSuperAdmin();

        if ($firstSuperAdmin->id == $user->id) {
            return Reply::error(__('superadmin.cannotDeleteFirstSuperadmin'));
        }

        $user->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function stopImpersonate()
    {
        $userAuthId = session('impersonate');
        $companyId = session('impersonate_company_id');
        session()->flush();
        Auth::logout();

        session(['stop_impersonate' => $userAuthId]);
        Auth::loginUsingId($userAuthId);

        return redirect(route('superadmin.companies.show', $companyId));
    }

    public function workspaces()
    {
        if (session()->has('multi_company_selected')) {
            return redirect(route('dashboard'));
        }

        $this->userCompanies = User::withoutGlobalScope(CompanyScope::class)->where('email', user()->email)
            ->with('company')->select('id', 'company_id', 'login')->get();

        return view('super-admin.workspaces', $this->data);
    }

    public function chooseWorkspace(Request $request)
    {
        $userId = $request->user_id;
        $companyId = $request->company_id;

        $company = Company::findOrFail($companyId);
        $user = User::withoutGlobalScope(CompanyScope::class)->findOrFail($userId);

        if ($user->login == 'disable') {
            return Reply::error(__('superadmin.loginRestricted'));
        }

        session(['company' => $company]);
        session(['multi_company_selected' => true]);
        session(['user' => $user]);
        session()->forget('user_roles');
        session()->forget('sidebar_user_perms');

        flushCompanySpecificSessions();
        Auth::loginUsingId($user->user_auth_id);

        return Reply::dataOnly(['status' => 'success', 'redirect_url' => RouteServiceProvider::HOME]);
    }

    // Change role for superAdmin by DataTable
    public function assignRole(Request $request)
    {
        $changeEmployeeRolePermission = user()->permission('change_superadmin_role');

        abort_403($changeEmployeeRolePermission != 'all');

        $userId = $request->userId;
        $roleId = $request->role;

        $this->changeRole($userId, $roleId);

        return Reply::success(__('messages.roleAssigned'));
    }

    // Change role for superAdmin
    private function changeRole($userId, $roleId)
    {

        if (!is_null($userId) && !is_null($roleId)) {
            $superadminRole = Role::withoutGlobalScopes()->findOrFail($roleId);

            $user = User::withoutGlobalScopes()->findOrFail($userId);

            RoleUser::where('user_id', $user->id)->delete();

            UserPermission::where('user_id', $userId)->delete();

            $user->roles()->attach($roleId);

            $userRole = RoleUser::where('user_id', $user->id)->first();

            if (is_null($userRole)) {
                $user->roles()->attach($superadminRole->id);
            }

            $user->assignUserRolePermission($roleId);

            $userSession = new AppSettingController();
            $userSession->deleteSessions([$user->id]);
        }
    }

    public function show($id)
    {
        return redirect()->route('superadmin.superadmin.index');
    }

}
