<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\UpdateThemeSetting;
use App\Models\GlobalSetting;
use App\Models\ThemeSetting;
use App\Scopes\CompanyScope;

class ThemeSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.themeSettings';
        $this->activeSettingMenu = 'theme_settings';
        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_theme_settings'));
            return $next($request);
        });
    }

    public function index()
    {
        $this->superAdminTheme = ThemeSetting::withoutGlobalScope(CompanyScope::class)->whereNull('company_id')->first();

        return view('super-admin.theme-settings.index', $this->data);
    }

    /**
     * @param UpdateThemeSetting $request
     * @return array
     */
    public function store(UpdateThemeSetting $request)
    {
        $superAdminTheme = ThemeSetting::withoutGlobalScope(CompanyScope::class)
            ->whereNull('company_id')
            ->where('panel', 'superadmin')
            ->first();

        $restrictAdminThemeChange = ($request->has('set_customer_theme') ? 1 : 0);

        $this->themeUpdate($superAdminTheme, $request->sidebar_theme, $request->primary_color, $restrictAdminThemeChange);

        $globalSetting = GlobalSetting::first();
        $globalSetting->logo_background_color = $request->logo_background_color;
        $globalSetting->auth_theme = $request->auth_theme;
        $globalSetting->global_app_name = $request->app_name;
        $globalSetting->header_color = $request->global_header_color;

        if ($request->logo_delete == 'yes') {
            Files::deleteFile($globalSetting->logo, 'app-logo');
            $globalSetting->logo = null;
        }

        if ($request->hasFile('logo_front')) {
            Files::deleteFile($globalSetting->logo_front, 'app-logo');
            $globalSetting->logo_front = Files::uploadLocalOrS3($request->logo_front, 'app-logo');
        }

        if ($request->hasFile('logo')) {
            Files::deleteFile($globalSetting->logo, 'app-logo');
            $globalSetting->logo = Files::uploadLocalOrS3($request->logo, 'app-logo');
        }

        if ($request->light_logo_delete == 'yes') {
            Files::deleteFile($globalSetting->light_logo, 'app-logo');
            $globalSetting->light_logo = null;
        }

        if ($request->hasFile('light_logo')) {
            Files::deleteFile($globalSetting->light_logo, 'app-logo');
            $globalSetting->light_logo = Files::uploadLocalOrS3($request->light_logo, 'app-logo');
        }

        if ($request->login_background_delete == 'yes') {
            Files::deleteFile($globalSetting->login_background, 'login-background');
            $globalSetting->login_background = null;
        }

        if ($request->hasFile('login_background')) {
            Files::deleteFile($globalSetting->login_background, 'login-background');
            $globalSetting->login_background = Files::uploadLocalOrS3($request->login_background, 'login-background');
        }

        if ($request->favicon_delete == 'yes') {
            Files::deleteFile($globalSetting->favicon, 'favicon');
            $globalSetting->favicon = null;
        }

        if ($request->hasFile('favicon')) {
            $globalSetting->favicon = Files::uploadLocalOrS3($request->favicon, 'favicon');
        }

        $globalSetting->sidebar_logo_style = $request->sidebar_logo_style;

        $globalSetting->save();

        session()->forget(['superadmin_theme', 'admin_theme']);
        cache()->forget('global_setting');

        return Reply::redirect(route('superadmin.settings.super-admin-theme-settings.index'), __('messages.updateSuccess'));
    }

    private function themeUpdate($updateObject, $sidebarTheme, $primaryColor, $restrictAdminThemeChange)
    {
        $updateObject->header_color = $primaryColor;
        $updateObject->sidebar_theme = $sidebarTheme;
        $updateObject->save();

        ThemeSetting::withoutGlobalScope(CompanyScope::class)->update(['restrict_admin_theme_change' => $restrictAdminThemeChange]);
    }

}
