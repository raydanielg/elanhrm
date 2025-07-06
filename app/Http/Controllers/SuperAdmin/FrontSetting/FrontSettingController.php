<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdateDetail;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdatePriceSetting;
use App\Http\Requests\SuperAdmin\ContactSetting\ContactSettingRequest;

class FrontSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.frontSettings';
        $this->activeSettingMenu = 'front_settings';
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
    public function index($lang = 'en')
    {
        $this->pageTitle = 'superadmin.menu.frontSettings';
        $this->activeSettingMenu = 'front_settings';
        $this->view = 'super-admin.front-setting.front-details.ajax.lang';

        $this->frontDetail = FrontDetail::first();

        $this->lang = LanguageSetting::where('language_code', $lang)->first();

        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::query()
            ->where('language_setting_id', $this->lang->id)
            ->first();

        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')
            ->whereNotNull('header_title')
            ->where('header_title', '<>', '')
            ->get()
            ->toArray();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.front-details.index', $this->data);
    }

    public function updateLang(UpdateDetail $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = $request->validated();

        $data['language_setting_id'] = $request->language_setting_id == 0 ? null : $request->language_setting_id;

        if ($request->hasFile('image')) {

            if (!is_null($row)) {
                Files::deleteFile($row->image, 'front');
            }

            $data['image'] = Files::uploadLocalOrS3($request->image, 'front');
        }

        if ($row) {
            $row->update($data);
        } else {
            $row = TrFrontDetail::create($data);
        }

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->header_title,
            'lang' => $row->language->language_code
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function authUpdate(Request $request)
    {
        $global = GlobalSetting::first();

        if ($global->login_ui == 1 && $global->front_design == 1) {
            $global->auth_css_theme_two = $request->auth_css;
        } else {
            $global->auth_css = $request->auth_css;
        }

        $global->save();
        cache()->forget('global_setting');

        return Reply::redirect(route('superadmin.front-settings.auth_settings'), __('messages.updateSuccess'));
    }

    public function contact()
    {
        $this->pageTitle = 'superadmin.menu.contactSetting';
        $this->activeSettingMenu = 'contact_settings';

        $this->frontDetail = FrontDetail::first();

        return view('super-admin.contact-settings.index', $this->data);
    }

    public function contactUpdate(ContactSettingRequest $request)
    {
        $setting = FrontDetail::first();
        $setting->address = $request->address;
        $setting->phone = $request->phone;
        $setting->email = $request->email;
        $setting->contact_html = $request->contact_html;

        $setting->save();

        if ($request->email) {
            $globalSetting = GlobalSetting::first();
            $globalSetting->email = $request->email;
            $globalSetting->saveQuietly();

            cache()->forget('global_setting');
        }

        return Reply::success(__('messages.updateSuccess'));
    }

    public function priceLang($lang = 'en')
    {
        $this->pageTitle = 'superadmin.menu.priceSetting';
        $this->activeSettingMenu = 'price_settings_translation';
        $this->view = 'super-admin.front-setting.front-details.ajax.lang-price';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::select(['price_title', 'price_description', 'language_setting_id'])->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('price_title')->get()->toArray();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.front-details.translation-price', $this->data);
    }

    public function updatePriceLang(UpdatePriceSetting $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = [
            'price_title' => $request->price_title,
            'price_description' => $request->price_description,
            'language_setting_id' => $request->language_setting_id !== 0 ? $request->language_setting_id : null
        ];

        if ($row) {
            $row->update($data);
        } else {
            $row = TrFrontDetail::create($data);
        }

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->price_title,
            'lang' => $row->language->language_code
        ]);
    }
}
