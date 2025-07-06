<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdateFrontMenu;
use App\Models\SuperAdmin\FrontMenu;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Http\Controllers\AccountBaseController;

class FrontMenuController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.frontMenuSettings';
        $this->activeSettingMenu = 'front_menu_settings';

        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_front_settings'));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lang($lang = 'en')
    {
        $this->lang = LanguageSetting::where('language_code', $lang)->first();

        $this->frontMenu = FrontMenu::where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = FrontMenu::select('language_setting_id')->get()->toArray();

        $this->activeTab = $this->lang->language_code;

        $this->view = 'super-admin.front-setting.front-menu-settings.ajax.lang';
        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.front-menu-settings.index', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // @codingStandardsIgnoreLine
    public function updateLang(UpdateFrontMenu $request)
    {
        $frontMenu = FrontMenu::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();
        $frontMenu->home = $request->home;
        $frontMenu->price = $request->price;
        $frontMenu->contact = $request->contact;
        $frontMenu->feature = $request->feature;
        $frontMenu->get_start = $request->get_start;
        $frontMenu->login = $request->login;
        $frontMenu->contact_submit = $request->contact_submit;
        $frontMenu->save();

        cache()->forget('front_menu');

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->message,
            'lang' => $frontMenu->language->language_code
        ]);
    }

}
