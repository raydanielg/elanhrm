<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Role;
use App\Models\SuperAdmin\SignUpSetting;
use App\Models\User;
use App\Helper\Reply;
use App\Models\Module;
use GuzzleHttp\Client;
use App\Models\Company;
use App\Models\GlobalSetting;
use App\Models\SuperAdmin\Feature;
use App\Models\SuperAdmin\Package;
use App\Notifications\NewCustomer;
use App\Models\SuperAdmin\FrontFaq;
use Illuminate\Support\Facades\App;
use App\Models\SuperAdmin\SeoDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin\FooterMenu;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\SuperAdmin\FrontClients;
use App\Models\SuperAdmin\FrontFeature;
use App\Models\SuperAdmin\Testimonials;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Models\SuperAdmin\PackageSetting;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\AccountBaseController;
use App\Notifications\SuperAdmin\ContactUsMail;
use App\Http\Requests\SuperAdmin\ContactUs\ContactUsRequest;
use App\Http\Requests\SuperAdmin\Register\StoreClientRequest;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\UserAuth;
use Illuminate\Support\Facades\DB;

class FrontendController extends FrontBaseController
{

    public function index($slug = null)
    {

        if ($this->global->setup_homepage == 'custom') {
            return response(file_get_contents($this->global->custom_homepage_url));
        }

        if ($this->global->setup_homepage == 'signup') {
            return $this->loadSignUpPage();
        }

        if ($this->global->setup_homepage == 'login') {
            return $this->loadLoginPage();
        }

        $this->seoDetail = SeoDetail::where('page_name', 'home')->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', 'home')->first();

        $this->pageTitle = $this->seoDetail ? $this->seoDetail->seo_title : __('app.menu.home');
        $this->currencies = GlobalCurrency::get();
        $this->packages = Package::where('default', 'no')
            ->where('currency_id', global_setting()->currency_id)
            ->where('is_private', 0)
            ->orderBy('sort', 'ASC')
            ->get();

        $localeLanguageId = optional($this->localeLanguage)->id;
        $counts = Feature::select(DB::raw('count(case when type = "image" then 1 end) as imageFeaturesCount'), DB::raw('count(case when type = "icon" then 1 end) as iconFeaturesCount'))
            ->where('language_setting_id', $localeLanguageId)
            ->groupBy('language_setting_id')
            ->first();

        $imageFeaturesCount = $counts->imageFeaturesCount ?? 0;
        $iconFeaturesCount = $counts->iconFeaturesCount ?? 0;

        $frontClientsCount = FrontClients::select('id', 'language_setting_id')->where('language_setting_id', $localeLanguageId)->count();
        $testimonialsCount = Testimonials::select('id', 'language_setting_id')->where('language_setting_id', $localeLanguageId)->count();

        $this->featureWithImages = Feature::where([
            'language_setting_id' => $imageFeaturesCount > 0 ? ($localeLanguageId) : null,
            'type' => 'image'
        ])->whereNull('front_feature_id')->get();

        $this->featureWithIcons = Feature::where([
            'language_setting_id' => $iconFeaturesCount > 0 ? ($localeLanguageId) : null,
            'type' => 'icon'
        ])->whereNull('front_feature_id')->get();

        $this->frontClients = FrontClients::where('language_setting_id', $frontClientsCount > 0 ? ($localeLanguageId) : null)->get();
        $this->testimonials = Testimonials::where('language_setting_id', $testimonialsCount > 0 ? ($localeLanguageId) : null)->get();

        $this->trialPackage = Package::where('default', 'trial')->first();

        // Check if trail is active
        $this->packageSetting = PackageSetting::where('status', 'active')->first();

        // Multi-Page design
        if ($this->global->front_design == 1) {

            if ($slug) {
                $this->slugData = FooterMenu::where('slug', $slug)->first();
                $this->pageTitle = $this->slugData->name;

                return view('super-admin.saas.footer-page', $this->data);
            }

            return view('super-admin.saas.home', $this->data);
        }

        // Single page design is selected
        $this->packageFeaturesModuleData = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->get();

        $this->packageFeatures = $this->packageFeaturesModuleData->pluck('module_name')->toArray();
        $this->packageModuleData = $this->packageFeaturesModuleData->pluck('module_name', 'id')->all();

        $this->activeModule = $this->packageFeatures;

        return view('super-admin.front.home', $this->data);

    }

    public function feature()
    {
        App::setLocale($this->locale);
        $this->seoDetail = SeoDetail::where('page_name', 'feature')->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', 'feature')->first();

        $this->pageTitle = isset($this->seoDetail) ? $this->seoDetail->seo_title : __('superadmin.menu.features');
        $types = ['task', 'bills', 'team', 'apps'];

        foreach ($types as $type) {
            $featureCount = Feature::select('id', 'language_setting_id', 'type')->where(['language_setting_id' => $this->localeLanguage ? $this->localeLanguage->id : null, 'type' => $type])->count();
            $this->data['feature' . ucfirst(str_plural($type))] = Feature::where([
                'language_setting_id' => $featureCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null,
                'type' => $type
            ])->get();
        }

        $frontClientsCount = FrontClients::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();
        $this->frontClients = FrontClients::where('language_setting_id', $frontClientsCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->get();
        $iconFeaturesCount = Feature::select('id', 'language_setting_id', 'type')->where(['language_setting_id' => $this->localeLanguage ? $this->localeLanguage->id : null, 'type' => 'icon'])->count();

        $this->frontFeatures = FrontFeature::with('features')->where([
            'language_setting_id' => $iconFeaturesCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null,
        ])->get();

        abort_if($this->setting->front_design != 1, 403);

        return view('super-admin.saas.feature', $this->data);
    }

    public function pricing()
    {
        abort_403($this->setting->front_design != 1);

        App::setLocale($this->locale);
        $this->seoDetail = SeoDetail::where('page_name', 'pricing')->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', 'pricing')->first();
        $this->pageTitle = isset($this->seoDetail) ? $this->seoDetail->seo_title : __('app.menu.pricing');


        $packageCurrencyId = request()->currencyId ?: global_setting()->currency_id;

        $this->packages = Package::where(function ($query) use ($packageCurrencyId) {
            $query->where(function ($q) use ($packageCurrencyId) {
                $q->where('default', 'no')
                  ->where('currency_id', $packageCurrencyId);
            })
            ->orWhere(function ($q) use ($packageCurrencyId) {
                $q->where('default', 'lifetime')
                  ->where('currency_id', $packageCurrencyId);
            })
            ->orWhere('is_free', 1);
        })
            ->where('is_private', 0)
            ->orderBy('sort', 'ASC')
            ->get();
        $this->packageFeaturesModuleData = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->get();

        $this->packageFeatures = $this->packageFeaturesModuleData->pluck('module_name')->toArray();
        $this->packageModuleData = $this->packageFeaturesModuleData->pluck('module_name', 'id')->all();

        $this->annualPlan = $this->packages->filter(function ($value) {
            return $value->annual_status == 1;
        })->count();

        $this->monthlyPlan = $this->packages->filter(function ($value) {
            return $value->monthly_status == 1;
        })->count();


        $this->activeModule = $this->packageFeatures;

        if (request()->ajax()) {
            return Reply::dataOnly(
                [
                    'view' => view('super-admin.saas.pricing-plan', $this->data)->render()
                ]
            );
        }

        $frontFaqsCount = FrontFaq::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();

        $this->frontFaqs = FrontFaq::where('language_setting_id', $frontFaqsCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->get();

        // Check if trail is active
        $this->packageSetting = PackageSetting::where('status', 'active')->first();
        $this->trialPackage = Package::where('default', 'trial')->first();

        $this->currencies = GlobalCurrency::get();

        return view('super-admin.saas.pricing', $this->data);
    }

    public function pricingPlan()
    {
        if (request()->ajax()) {
            $packageCurrencyId = request()->currencyId ?: global_setting()->currency_id;
            $this->packages = Package::where(function ($query) use ($packageCurrencyId) {
                $query->where(function ($q) use ($packageCurrencyId) {
                    $q->where('default', 'no')
                      ->where('currency_id', $packageCurrencyId);
                })
                ->orWhere(function ($q) use ($packageCurrencyId) {
                    $q->where('default', 'lifetime')
                      ->where('currency_id', $packageCurrencyId);
                })
                ->orWhere('is_free', 1);
            })
                ->where('is_private', 0)
                ->orderBy('sort', 'ASC')
                ->get();

            $this->packageFeaturesModuleData = Module::where('module_name', '<>', 'settings')
                ->where('module_name', '<>', 'dashboards')
                ->where('module_name', '<>', 'restApi')
                ->whereNotIn('module_name', Module::disabledModuleArray())
                ->get();

            $this->packageFeatures = $this->packageFeaturesModuleData->pluck('module_name')->toArray();
            $this->packageModuleData = $this->packageFeaturesModuleData->pluck('module_name', 'id')->all();

            $this->activeModule = $this->packageFeatures;

            return Reply::dataOnly(
                [
                    'view' => view('super-admin.front.section.pricing-plan', $this->data)->render()
                ]
            );
        }
    }

    public function contact()
    {
        App::setLocale($this->locale);
        $this->seoDetail = SeoDetail::where('page_name', 'contact')->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', 'contact')->first();
        $this->pageTitle = $this->seoDetail ? $this->seoDetail->seo_title : __('app.menu.contact');

        abort_if($this->setting->front_design != 1, 403);

        return view('super-admin.saas.contact', $this->data);
    }

    public function page($slug = null)
    {
        App::setLocale($this->locale);
        $this->slugData = FooterMenu::where('slug', $slug)->where('language_setting_id', $this->localeLanguage->id)->firstOrFail();

        $this->seoDetail = SeoDetail::where('page_name', $this->slugData->slug)->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', $this->slugData->slug)->first();
        $this->pageTitle = $this->slugData->name;

        if ($this->setting->front_design == 1) {
            return view('super-admin.saas.footer-page', $this->data);
        }

        return view('super-admin.front.footer-page', $this->data);
    }

    public function contactUs(ContactUsRequest $request)
    {
        $this->recaptchaValidate($request);
        $this->pageTitle = 'superadmin.menu.contact';
        $generatedBys = User::allSuperAdmin();
        $frontDetails = FrontDetail::first();
        $this->table = '<table><tbody style="color:#0000009c;">
        <tr>
            <td><p>Name : </p></td>
            <td><p>' . $request->name . '</p></td>
        </tr>
        <tr>
            <td><p>Email : </p></td>
            <td><p>' . $request->email . '</p></td>
        </tr>
        <tr>
            <td style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;min-width: 98px;vertical-align: super;"><p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">' . __('app.message') . ' : </p></td>
            <td><p>' . $request->message . '</p></td>
        </tr>
</tbody>

</table><br>';

        if ($frontDetails->email) {
            Notification::route('mail', $frontDetails->email)
                ->notify(new ContactUsMail($this->data));

        }
        else {
            Notification::route('mail', $generatedBys)
                ->notify(new ContactUsMail($this->data));
        }


        return Reply::success(__('superadmin.contactUsMessage'));
    }

    public function recaptchaValidate($request)
    {
        $global = global_setting();

        if ($global->google_recaptcha_status == 'active') {
            $gRecaptchaResponseInput = 'g-recaptcha-response';

            $gRecaptchaResponse = $global->google_captcha_version == 'v2' ? $request->{$gRecaptchaResponseInput} : $request->get('recaptcha_token');
            $validateRecaptcha = $this->validateGoogleRecaptcha($gRecaptchaResponse);

            if (!$validateRecaptcha) {
                return false;
            }
        }

        return true;
    }

    public function validateGoogleRecaptcha($googleRecaptchaResponse)
    {
        $setting = GlobalSetting::first();

        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => $setting->google_recaptcha_secret,
                    'response' => $googleRecaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    public function changeLanguage($lang)
    {
        // set the language session and redirect back
        session(['language' => $lang]);
        $lang = LanguageSetting::where('language_code', $lang)->first()->is_rtl;
        session()->forget('changedRtl');
        session(['changedRtl' => $lang == true ? true : false]);

        return redirect()->back();
    }

    public function loadSignUpPage()
    {
        if (\user()) {
            return redirect(getDomainSpecificUrl(route('login'), \user()->company));
        }

        $this->seoDetail = SeoDetail::where('page_name', 'home')->where('language_setting_id', $this->localeLanguage?->id)->first() ?: SeoDetail::where('page_name', 'home')->first();
        $this->pageTitle = __('app.signup');

        $view = ($this->setting->front_design == 1) ? 'super-admin.saas.register' : 'super-admin.front.register';


        if ($this->global->frontend_disable) {
            $view = 'auth.register';
        }

        $signUpCount = SignUpSetting::select('id', 'language_setting_id')->where('language_setting_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();
        $this->signUpMessage = SignUpSetting::where('language_setting_id', $signUpCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->first();

        $this->trFrontDetail = TrFrontDetail::where('language_setting_id', $this->localeLanguage->id)->first();
        $this->trFrontDetail = $this->trFrontDetail ?: TrFrontDetail::where('language_setting_id', $this->enLocaleLanguage->id)->first();

        $this->registrationStatus = $this->global;

        return view($view, $this->data);
    }

    public function loadLoginPage()
    {
        if (\user()) {
            return redirect(getDomainSpecificUrl(route('login'), \user()->company));
        }

        if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }

        if ($this->global->frontend_disable) {
            return view('auth.login', $this->data);
        }

        if (module_enabled('Subdomain')) {
            $this->pageTitle = __('subdomain::app.core.workspaceTitle');

            $view = ($this->setting->front_design == 1) ? 'subdomain::saas.workspace' : 'subdomain::workspace';

            return view($view, $this->data);
        }

        if ($this->setting->front_design == 1 && $this->setting->login_ui == 1) {
            return view('super-admin.saas.login', $this->data);
        }

        $this->pageTitle = 'Login Page';

        return view('auth.login', $this->data);
    }

    public function clientSignup(Company $company)
    {
        $this->company = $company;

        return view('super-admin.front.client-signup', $this->data);
    }

    public function clientRegister(StoreClientRequest $request, Company $company)
    {
        if (!$company->allow_client_signup) {
            return abort(403, __('messages.clientSignUpDisabledByAdmin'));
        }

        DB::beginTransaction();

        $userAuth = UserAuth::createUserAuthCredentials($request->email, $request->password);

        if(global_setting()->company_need_approval === 1 || global_setting()->email_verification === 1){
            $approval = 0;
        }else{
            $approval = 1;
        }

        $user = User::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'admin_approval' => $approval,
            'user_auth_id' => $userAuth->id
        ]);

        $user->clientDetails()->create(['company_name' => $request->company_name]);

        $role = Role::where('company_id', $company->id)->where('name', 'client')->select('id')->first();
        $user->attachRole($role->id);

        $user->assignUserRolePermission($role->id);

        $log = new AccountBaseController();

        // Log search
        $log->logSearchEntry($user->id, $user->name, 'clients.show', 'client');

        if (!is_null($user->email)) {
            $log->logSearchEntry($user->id, $user->email, 'clients.show', 'client');
        }

        if (!is_null($user->clientDetails->company_name)) {
            $log->logSearchEntry($user->id, $user->clientDetails->company_name, 'clients.show', 'client');
        }

        Notification::send(User::allAdmins($user->company->id), new NewCustomer($user));

        session(['company' => $company]);
        session(['user' => $user]);

        // login user
        Auth::login($userAuth, true);
        DB::commit();

        return Reply::redirect(route('dashboard'), __('superadmin.clientRegistrationSuccess'));
    }

}
