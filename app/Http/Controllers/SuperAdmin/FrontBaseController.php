<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\FooterMenu;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\SuperAdmin\FrontMenu;
use App\Models\SuperAdmin\FrontWidget;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class FrontBaseController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->showInstall();
        $this->middleware(function ($request, $next) {

            $this->frontDetail = FrontDetail::first();
            $this->languages = language_setting();
            $this->global = $this->globalSetting = $this->setting = global_setting();

            $this->locale = $this->frontDetail->locale;

            if (session()->has('language')) {
                $this->locale = session('language');
            }

            App::setLocale($this->locale);
            Carbon::setLocale($this->locale);
            setlocale(LC_TIME, $this->locale . '_' . strtoupper($this->locale));

            $this->enLocaleLanguage = language_setting_locale('en');
            $this->localeLanguage = $this->locale != 'en' ? language_setting_locale($this->locale) : $this->enLocaleLanguage;
            $this->localeLanguage = $this->localeLanguage ?: $this->enLocaleLanguage;

            $this->footerSettings = FooterMenu::whereNotNull('slug')
                ->where('private', 0)
                ->where('language_setting_id', $this->localeLanguage->id)
                ->get();

            $this->footerSettings = $this->footerSettings->count() > 0 ? $this->footerSettings : FooterMenu::whereNotNull('slug')->where('private', 0)
                ->where('language_setting_id', $this->enLocaleLanguage->id)
                ->get();

            $this->frontMenu = FrontMenu::where('language_setting_id', $this->localeLanguage->id)->first();
            $this->frontMenu = $this->frontMenu ?: FrontMenu::where('language_setting_id', $this->enLocaleLanguage->id)->first();

            $this->frontWidgets = FrontWidget::all();

            $this->detail = $this->frontDetail;

            $this->trFrontDetail = TrFrontDetail::where('language_setting_id', $this->localeLanguage->id)->first();
            $this->trFrontDetail = $this->trFrontDetail ?: TrFrontDetail::where('language_setting_id', $this->enLocaleLanguage->id)->first();

            // ACCOUNT SETUP REDIRECT
            $userTotal = User::count();

            if ($userTotal == 0 && !module_enabled('Subdomain')) {
                return redirect()->route('login');
            }

            return $next($request);
        });

    }

}
