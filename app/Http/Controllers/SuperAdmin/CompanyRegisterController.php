<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Register\StoreRequest;
use App\Models\Company;
use App\Models\EmployeeDetails;
use App\Models\GlobalSetting;
use App\Models\Role;
use App\Models\SuperAdmin\SeoDetail;
use App\Models\SuperAdmin\SignUpSetting;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Models\UniversalSearch;
use App\Models\User;
use App\Models\UserAuth;
use App\Notifications\NewUser;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportException;

class CompanyRegisterController extends FrontBaseController
{

    public function index()
    {
        $this->global = GlobalSetting::first();

        $user = user();

        if ($user) {
            if (!is_null($user->company_id)) {
                return redirect(getDomainSpecificUrl(route('login'), $user->company));
            }

            // Redirect superadmin
            return redirect(getDomainSpecificUrl(route('login')));
        }

        $this->seoDetail = SeoDetail::where('page_name', 'home')->first();
        $this->pageTitle = __('app.signup');

        $view = ($this->setting->front_design == 1) ? 'super-admin.saas.register' : 'super-admin.front.register';


        if ($this->global->frontend_disable || $this->global->setup_homepage == 'custom') {
            $view = 'super-admin.register';
        }


        $this->trFrontDetail = TrFrontDetail::where('language_setting_id', $this->localeLanguage->id)->first();
        $this->trFrontDetail = $this->trFrontDetail ?: TrFrontDetail::where('language_setting_id', $this->enLocaleLanguage->id)->first();

        $signUpCount = SignUpSetting::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();
        $this->signUpMessage = SignUpSetting::where('language_setting_id', $signUpCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->first();

        $this->registrationStatus = $this->global;

        return view($view, $this->data);
    }

    public function store(StoreRequest $request)
    {

        $global = GlobalSetting::first();

        if (!$global->registration_open) {
            abort_403('Registration Disabled');
        }

        if ($global->google_recaptcha_status == 'active' && !$this->recaptchaValidate($request)) {
            return Reply::error('Recaptcha not validated.');
        }

        DB::beginTransaction();

        try {
            $company = new Company();
            $company->company_name = $request->company_name;
            $company->company_email = $request->email;
            $company->address = $request->company_name;
            $company->app_name = $request->company_name;

            if (module_enabled('Subdomain')) {
                $company->sub_domain = strtolower($request->sub_domain);
            }

            $company->save();

            $user = $this->addUser($company, $request, $global);

            DB::commit();

            if (!$global->company_need_approval) {
                if (!module_enabled('Subdomain')) {
                    Auth::loginUsingId($user->user_auth_id);
                }

            }
            else {
                session()->flash('company_approval_pending', __('auth.failedCompanyUnapproved'));

                return Reply::redirect(route('front.signup.index'));
            }


        } catch (TransportException $e) {
            DB::rollback();

            return Reply::error('Please contact administrator to set SMTP details to add company.<br>' . $e->getMessage(), 'smtp_error');
        } catch (\Exception $e) {
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support: ' . $e->getMessage());
        }

        return Reply::redirect(getDomainSpecificUrl(route('login'), $company), __('superadmin.signUpThankYou'));
    }

    public function getEmailVerification($code)
    {
        $this->pageTitle = 'modules.accountSettings.emailVerification';
        $this->message = User::emailVerify($code);

        return view('auth.email-verification', $this->data);
    }

    public function addUser($company, $request, $global)
    {
        // Save Admin
        $user = User::withoutGlobalScopes([CompanyScope::class, ActiveScope::class])
            ->where('company_id', $company->id)
            ->where('email', $request->email)
            ->first();

        if (is_null($user)) {
            $user = new User();
        }

        $userAuth = UserAuth::createUserAuthCredentials($request->email);

        $countryId = User::firstSuperAdmin()?->country_id ?? null;
        $user->company_id = $company->id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = 'active';
        $user->user_auth_id = $userAuth->id;
        $user->locale = $company->locale;
        $user->country_id = $countryId;
        $user->save();

        if ($global->email_verification && !$global->company_need_approval && !module_enabled('Subdomain')) {
            $userAuth->sendEmailVerificationNotification();
        }

        if ($request->password != '') {
            UserAuth::where('id', $user->user_auth_id)->update(['password' => bcrypt($request->password)]);
            $user->notify(new NewUser($user, $request->password));
        }

        if (!$user->hasRole('admin')) {

            // Attach Admin Role
            $adminRole = Role::withoutGlobalScope(CompanyScope::class)->where('name', 'admin')->where('company_id', $company->id)->first();

            $employeeRole = Role::withoutGlobalScope(CompanyScope::class)->where('name', 'employee')->where('company_id', $company->id)->first();

            $user->roles()->attach($adminRole->id);
            $this->addEmployeeDetails($user, $employeeRole, $company->id);

            $user->assignUserRolePermission($adminRole->id);

        }

        return $user;
    }

    private function addEmployeeDetails($user, $employeeRole, $companyId)
    {
        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->company_id = $companyId;
        /* @phpstan-ignore-line */
        $employee->employee_id = 'EMP-1';
        /* @phpstan-ignore-line */
        $employee->save();

        $search = new UniversalSearch();
        $search->searchable_id = $user->id;
        $search->company_id = $companyId;
        $search->title = $user->name;
        $search->route_name = 'employees.show';
        $search->save();

        // Assign Role
        $user->roles()->attach($employeeRole->id);
        /* @phpstan-ignore-line */
    }

    public function recaptchaValidate($request)
    {
        $global = global_setting();

        if ($global->google_recaptcha_status == 'active') {

            $gRecaptchaResponseInput = global_setting()->google_recaptcha_v3_status == 'active' ? 'g_recaptcha' : 'g-recaptcha-response';
            $gRecaptchaResponse = $request[$gRecaptchaResponseInput];

            $validateRecaptcha = GlobalSetting::validateGoogleRecaptcha($gRecaptchaResponse);

            if (!$validateRecaptcha) {
                return $this->googleRecaptchaMessage();
            }
        }

        return true;
    }

    /**
     * @throws ValidationException
     */
    public function googleRecaptchaMessage()
    {
        throw ValidationException::withMessages([
            'g-recaptcha-response' => [__('auth.recaptchaFailed')],
        ]);
    }

}
