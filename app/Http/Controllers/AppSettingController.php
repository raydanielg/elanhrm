<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin\GlobalCurrency;
use DateTimeZone;
use App\Helper\Reply;
use App\Models\Company;
use App\Models\Session;
use App\Models\Currency;
use App\Models\User;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Admin\App\UpdateAppSetting;
use App\Models\SuperAdmin\FrontDetail;

class AppSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.appSettings';
        $this->activeSettingMenu = 'app_settings';

        $this->middleware(function ($request, $next) {
            abort_403(user()->permission('manage_app_setting') !== 'all' && GlobalSetting::validateSuperAdmin('manage_superadmin_app_settings'));

            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $tab = request('tab');

        switch ($tab) {

        case 'file-upload-setting':
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_app_settings'));

            $this->view = 'app-settings.ajax.file-upload-setting';
            break;
        case 'client-signup-setting':
            abort_403(user()->permission('manage_app_setting') !== 'all');
            $this->view = 'app-settings.ajax.client-signup-setting';
            break;
        case 'google-map-setting':
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_app_settings'));

            $this->view = 'app-settings.ajax.map-setting';
            break;
        default:
            $this->view = 'app-settings.ajax.app-setting';
            break;
        }

        $this->dateFormat = array_keys(Company::DATE_FORMATS);
        $this->timezones = DateTimeZone::listIdentifiers();
        $this->currencies = Currency::all();

        // ISWORKSUITESAAS
        if(user()->is_superadmin){
            $this->currencies = GlobalCurrency::all();
        }

        $this->dateObject = now();
        $this->cachedFile = File::exists(base_path('bootstrap/cache/config.php'));

        // Not fetching from session
        $this->globalSetting = GlobalSetting::first();

        $this->activeTab = $tab ?: 'app-setting';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('app-settings.index', $this->data);
    }

    /**
     * @param UpdateAppSetting $request
     * @param mixed $id
     * @return array
     * @throws BindingResolutionException
     * @throws CommandNotFoundException
     */
    // phpcs:ignore
    public function update(UpdateAppSetting $request, $id)
    {
        $tab = request('page');

        switch ($tab) {
        case 'file-upload-setting':
            isWorksuiteSaas() ? $this->updateFileUploadSetting($request) : '';
            break;
        case 'client-signup-setting':
            $this->updateClientSignupSetting($request);
            break;
        case 'google-map-setting':
            isWorksuiteSaas() ? $this->updateGoogleMapSetting($request) : '';
            break;
        default:
            $this->updateAppSetting($request);
            break;
        }

        session()->forget('company');
        cache()->forget('global_setting');
        session()->forget('companyOrGlobalSetting');

        return Reply::success(__('messages.updateSuccess'));
    }

    public function globalSettingSave($request)
    {
        $globalSetting = GlobalSetting::first();

        $globalSetting->currency_id = $request->currency_id;
        $globalSetting->moment_format = $this->momentFormat($globalSetting->date_format);

        $globalSetting->app_debug = $request->has('app_debug') && $request->app_debug == 'on' ? 1 : 0;
        $globalSetting->system_update = $request->has('system_update') && $request->system_update == 'on' ? 1 : 0;
        $globalSetting->session_driver = $request->session_driver;
        $globalSetting->timezone = $request->timezone;
        $globalSetting->locale = $request->locale;
        $globalSetting->date_format = $request->date_format;
        $globalSetting->time_format = $request->time_format;

        // WORKSUITESAAS
        if (user()->is_superadmin) {
            $globalSetting->company_need_approval = $request->company_need_approval == 'on' ? 1 : 0;
            $globalSetting->currency_id = $request->currency_id;
            $globalSetting->email_verification = $request->email_verification == 'on' ? 1 : 0;
        }

        $globalSetting->moment_format = $this->momentFormat($globalSetting->date_format);
        $globalSetting->datatable_row_limit = $request->datatable_row_limit;
        $globalSetting->save();
    }

    public function updateAppSetting($request)
    {
        // WORKSUITESAAS
        if(!user()->is_superadmin){
            $setting = company();
            $setting->currency_id = $request->currency_id;
            $setting->timezone = $request->timezone;
            $setting->locale = $request->locale;
            $setting->date_format = $request->date_format;
            $setting->time_format = $request->time_format;
            $setting->moment_format = $this->momentFormat($setting->date_format);
            $setting->dashboard_clock = $request->has('dashboard_clock') && $request->dashboard_clock == 'on' ? 1 : 0;
            $setting->employee_can_export_data = $request->has('employee_can_export_data') && $request->employee_can_export_data == 'on' ? 1 : 0;
            $setting->datatable_row_limit = $request->datatable_row_limit;
            $setting->save();
            $setting->refresh();
        }

        Artisan::call('update-exchange-rate');

        // Delete all sessions except the current user session because we can't change sessions for all the users
        DB::table('sessions')->where('user_id', '<>', user()->id)->delete();

        // Doing this is going to change the locale for self profile also. So as customer do not have to visit
        // Profile to change the locale
        $user = user();
        $user->locale = $request->locale;
        $user->saveQuietly();

        $globalSetting = companyOrGlobalSetting();
        $globalSetting->update(['locale' => $request->locale]);

        // WORKSUITESAAS change language for front detail
        FrontDetail::first()->update(['locale' => $request->locale]);

        session()->forget('isRtl');
        // WORKSUITESAAS
        if(!user()->is_superadmin){
            if ($request->currency_id) {
                \session()->forget('currency_format_setting');
                currency_format_setting($setting->currency_id);
            }
        }

        // WORKSUITESAAS
        if(user()->is_superadmin){
            $this->globalSettingSave($request);
        }

        session(['user' => User::find($user->id)]);

        $this->resetCache();
    }

    public function updateFileUploadSetting($request)
    {
        if (!empty($request->allowed_file_types)) {
            $allowed_file_types = $request->allowed_file_types;

            $fileTypeArray = [];

            foreach (json_decode($allowed_file_types) as $file) {
                $fileTypeArray[] = $file->value;
            }
        }

        $globalSetting = GlobalSetting::first();
        $globalSetting->allowed_file_types = !empty($fileTypeArray) ? implode(',', $fileTypeArray) : '';
        $globalSetting->allowed_file_size = $request->allowed_file_size;
        $globalSetting->allow_max_no_of_files = $request->allow_max_no_of_files;
        $globalSetting->save();
    }

    public function updateClientSignupSetting($request)
    {
        $setting = \company();
        $setting->allow_client_signup = $request->allow_client_signup == 'on' ? 1 : 0;
        $setting->admin_client_signup_approval = $request->admin_client_signup_approval == 'on' ? 1 : 0;
        $setting->save();
    }

    public function updateGoogleMapSetting(UpdateAppSetting $request)
    {
        $globalSetting = \global_setting();
        $globalSetting->google_map_key = $request->google_map_key;
        $globalSetting->save();
        cache()->forget('global_setting');
    }

    /**
     * @param string $dateFormat
     * @return string
     */
    public function momentFormat($dateFormat)
    {
        $availableDateFormats = Company::DATE_FORMATS;

        return (isset($availableDateFormats[$dateFormat])) ? $availableDateFormats[$dateFormat] : 'DD-MM-YYYY';
    }

    public function resetCache()
    {
        if (request()->cache) {
            try {
                Artisan::call('optimize');
                Artisan::call('route:clear');

            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }
        else {
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');
        }

        cache()->flush();

        return Reply::success(__('messages.cacheClear'));
    }

    public function refreshCache()
    {
        cache()->flush();

        if (File::exists(base_path('bootstrap/cache/config.php'))) {
            try {
                Artisan::call('optimize:clear');
                Artisan::call('cache:clear');
                Artisan::call('optimize');

            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }
        else {
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');
        }

        return Reply::success(__('messages.cacheClear'));
    }

    public function deleteSessions(array $usersIds = [])
    {
        if (!empty($usersIds)) {
            Session::whereIn('user_id', $usersIds)->where('user_id', '<>', user()->id)->delete();

            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::success(__('messages.deleteSuccess'));
    }

}
