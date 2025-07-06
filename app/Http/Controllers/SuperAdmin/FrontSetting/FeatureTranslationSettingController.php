<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\FeatureTranslation\StoreRequest;
use App\Http\Controllers\AccountBaseController;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\TrFrontDetail;

class FeatureTranslationSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.featureTranslation';
        $this->activeSettingMenu = 'feature_translation';

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
        $this->pageTitle = 'superadmin.menu.featureTranslation';
        $this->activeSettingMenu = 'feature_translation';

        $this->view = 'super-admin.front-setting.feature-translation-setting.ajax.lang';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::where('language_setting_id', $this->lang->id)->first();

        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->where(function($q){
            return $q->whereNotNull('feature_title')
                ->orWhereNotNull('favourite_apps_title');
        })->get()->toArray();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.feature-translation-setting.index', $this->data);
    }

    public function updateLang(StoreRequest $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        if (is_null($row)) {
            $row = new TrFrontDetail();
        }

        $row->feature_title = $request->feature_title;
        $row->feature_description = $request->feature_detail;
        $row->favourite_apps_title = $request->feature_app_title;
        $row->favourite_apps_detail = $request->feature_app_detail;
        $row->language_setting_id = $request->language_setting_id;
        $row->save();

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->feature_title ? $request->feature_title : $request->feature_app_title,
            'lang' => $row->language->language_code
        ]);
    }

}
