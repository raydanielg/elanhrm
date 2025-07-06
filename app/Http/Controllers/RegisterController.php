<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Helper\Reply;
use App\Http\Requests\User\AcceptInviteRequest;
use App\Http\Requests\User\AccountSetupRequest;
use App\Models\EmployeeDetails;
use App\Models\GlobalSetting;
use App\Models\Company;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\UniversalSearch;
use App\Models\UserAuth;
use App\Models\UserInvitation;
use Database\Seeders\SuperAdminUsersTableSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\NewUserRegistrationViaInviteEvent;
use Symfony\Component\Mailer\Exception\TransportException;
use App\Models\SuperAdmin\FrontWidget;

class RegisterController extends Controller
{

    public function invitation($code)
    {
        if (Auth::check()) {
            return redirect(route('dashboard'));
        }

        $this->invite = UserInvitation::where('invitation_code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $this->isAllowedInCurrentPackage = checkCompanyCanAddMoreEmployees($this->invite->company_id);

        $this->globalSetting = GlobalSetting::first();
        $this->frontWidgets = FrontWidget::all();

        return view('auth.invitation', $this->data);
    }

    public function acceptInvite(AcceptInviteRequest $request)
    {
        $invite = UserInvitation::where('invitation_code', $request->invite)
            ->where('status', 'active')
            ->first();

        $this->company = $invite->company;

        if (!checkCompanyCanAddMoreEmployees($invite->company_id) || (is_null($invite) || ($invite->invitation_type == 'email' && $request->email != $invite->email))) {
            return Reply::error('messages.acceptInviteError');
        }

        DB::beginTransaction();
        try {
            $userAuth = UserAuth::createUserAuthCredentials($request->email, $request->password);

            if(global_setting()->company_need_approval === 1 || global_setting()->email_verification === 1){
                $approval = 0;
            }else{
                $approval = 1;
            }

            $user = new User();
            $user->name = $request->name;
            $user->company_id = $invite->company_id;
            $user->email = $request->email;
            $user->admin_approval = $approval;
            $user->user_auth_id = $userAuth->id;

            $user->save();
            $user = $user->setAppends([]);

            $lastEmployeeID = EmployeeDetails::where('company_id', $invite->company_id)->count();
            $checkifExistEmployeeId = EmployeeDetails::select('id')->where('employee_id', ($lastEmployeeID + 1))->where('company_id', $invite->company_id)->first();

            if ($user->id) {
                $employee = new EmployeeDetails();
                $employee->user_id = $user->id;
                $employee->company_id = $invite->company_id;
                $employee->employee_id = ((!$checkifExistEmployeeId) ? ($lastEmployeeID + 1) : null);
                $employee->joining_date = now($this->company->timezone)->format('Y-m-d');
                $employee->added_by = $user->id;
                $employee->last_updated_by = $user->id;
                $employee->save();
            }

            $employeeRole = Role::where('name', 'employee')->where('company_id', $invite->company_id)->first();
            $user->attachRole($employeeRole);

            $user->assignUserRolePermission($employeeRole->id);

            $logSearch = new AccountBaseController();
            $logSearch->logSearchEntry($user->id, $user->name, 'employees.show', 'employee');

            if ($invite->invitation_type == 'email') {
                $invite->status = 'inactive';
                $invite->save();
            }

            // Commit Transaction
            DB::commit();

            // Send Notification to all admins about recently added member
            $admins = User::allAdmins($user->company->id);

            foreach ($admins as $admin) {
                event(new NewUserRegistrationViaInviteEvent($admin, $user));
            }

            if (isWorksuiteSaas() && $this->global->email_verification) {
                $userAuth->sendEmailVerificationNotification();
            }

            session()->forget('user');
            Auth::login($userAuth);

            return Reply::success(__('messages.signupSuccess'));
        } catch (TransportException $e) {
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Please configure SMTP details. Visit Settings -> notification setting to set smtp: ' . $e->getMessage(), 'smtp_error');
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support: ' . $e->getMessage());
        }

        return view('auth.invitation', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function setupAccount(AccountSetupRequest $request)
    {
        if (isWorksuiteSaas()) {
            $this->saasSetup($request);
            return Reply::success('Worksuite Application account created successfully. You will redirected to dashboard soon');
        }

        // Update company name
        $setting = Company::firstOrCreate();
        $setting->company_name = $request->company_name;
        $setting->app_name = $request->company_name;
        $setting->timezone = 'Asia/Kolkata';
        $setting->date_picker_format = 'dd-mm-yyyy';
        $setting->moment_format = 'DD-MM-YYYY';
        $setting->rounded_theme = 1;
        $setting->save();

        // Create admin user
        $user = new User();
        $user->name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->company_id = $setting->id;
        $user->save();

        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = $user->id;
        $employee->company_id = $setting->id;
        $employee->save();

        $search = new UniversalSearch();
        $search->searchable_id = $user->id;
        $search->title = $user->name;
        $search->route_name = 'employees.show';
        $search->save();

        // Attach roles
        $adminRole = Role::where('company_id', $setting->id)->where('name', 'admin')->first();
        $employeeRole = Role::where('company_id', $setting->id)->where('name', 'employee')->first();
        $user->roles()->attach($adminRole->id);
        $user->roles()->attach($employeeRole->id);

        $allPermissions = Permission::orderBy('id')->get()->pluck('id')->toArray();

        foreach ($allPermissions as $permission) {
            $user->permissionTypes()->attach([$permission => ['permission_type_id' => PermissionType::ALL]]);
        }

        Auth::login($user);

        return Reply::success(__('messages.signupSuccess'));
    }

    private function saasSetup($request)
    {
        $globalSetting = GlobalSetting::firstOrCreate();
        $globalSetting->global_app_name = $request->company_name;
        $globalSetting->locale = 'en';
        $globalSetting->google_recaptcha_status = 'deactive';
        $globalSetting->google_recaptcha_v2_status = 'deactive';
        $globalSetting->google_recaptcha_v3_status = 'deactive';
        $globalSetting->app_debug = false;

        // WORKSUITESAAS
        $globalCurrency = GlobalCurrency::first();
        $globalSetting->currency_id = $globalCurrency->id;

        $globalSetting->rtl = false;
        $globalSetting->hide_cron_message = 0;
        $globalSetting->system_update = 1;
        $globalSetting->show_review_modal = 1;
        $globalSetting->auth_theme = 'light';
        $globalSetting->allowed_file_size = 10;
        $globalSetting->moment_format = 'DD-MM-YYYY';
        $globalSetting->sidebar_logo_style = 'square';
        $globalSetting->allowed_file_types = 'image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip-compressed, application/x-compressed, multipart/x-zip,.xlsx,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,application/sla,.stl';
        $globalSetting->show_update_popup = 1;
        $globalSetting->hash = md5(microtime());
        $globalSetting->save();

        // Create admin user
        $superadmin = new User();
        $superadmin->is_superadmin = true;
        $superadmin->name = $request->full_name;
        $superadmin->email = $request->email;
        $superadmin->save();

        $userAuth = UserAuth::create(['email' => $superadmin->email, 'password' => bcrypt($request->password), 'email_verified_at' => now()]);
        $superadmin->user_auth_id = $userAuth->id;
        $superadmin->saveQuietly();

        SuperAdminUsersTableSeeder::superadminRolePermissionAttach($superadmin);

        // Update company name
        $setting = Company::firstOrCreate();
        $setting->company_name = 'Demo Company';
        $setting->app_name = 'Demo Company';
        $setting->timezone = 'Asia/Kolkata';
        $setting->date_picker_format = 'dd-mm-yyyy';
        $setting->moment_format = 'DD-MM-YYYY';
        $setting->rounded_theme = 1;
        $setting->save();

        // Create admin user
        $user = new User();
        $user->name = 'Admin';
        // Check if superadmin email is not same
        $user->email = ($request->email !== 'admin@example.com') ? 'admin@example.com' : 'admin@test.com';
        $user->company_id = $setting->id;
        $user->save();

        $userAuth = UserAuth::create(['email' => $user->email, 'password' => bcrypt('123456'), 'email_verified_at' => now()]);
        $user->user_auth_id = $userAuth->id;
        $user->saveQuietly();

        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = 'EMP-1';
        $employee->company_id = $setting->id;
        $employee->save();

        $search = new UniversalSearch();
        $search->searchable_id = $user->id;
        $search->title = $user->name;
        $search->route_name = 'employees.show';
        $search->save();

        // Attach roles
        $adminRole = Role::where('company_id', $setting->id)->where('name', 'admin')->first();
        $employeeRole = Role::where('company_id', $setting->id)->where('name', 'employee')->first();
        $user->roles()->attach($adminRole->id);
        $user->roles()->attach($employeeRole->id);

        $allPermissions = Permission::orderBy('id')->get()->pluck('id')->toArray();

        foreach ($allPermissions as $permission) {
            $user->permissionTypes()->attach([$permission => ['permission_type_id' => PermissionType::ALL]]);
        }

        Auth::loginUsingId($superadmin->user_auth_id);
    }

}
