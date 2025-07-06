<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\TrFrontDetail;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\SeoDetail;
use App\Models\SuperAdmin\FooterMenu;
use App\Http\Controllers\AccountBaseController;
use App\Models\GlobalSetting;

class SeoDetailController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.seoDetails';
        $this->activeSettingMenu = 'seo_details';

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
        $this->view = 'super-admin.front-setting.seo-detail.ajax.lang';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::select('footer_copyright_text', 'language_setting_id')->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('footer_copyright_text')->get()->toArray();

        $this->footer = FooterMenu::with('language:id,language_name,flag_code')->where('language_setting_id', $this->lang->id)->get();

        $this->seoDetails = SeoDetail::where('language_setting_id', $this->lang->id)->get();
        $footerMenu = FooterMenu::where('language_setting_id', $this->lang->id)->pluck('slug')->toArray();
        $footerMenu[] = 'home';

        if ($this->global->front_design == 0) {
            $this->seoDetails = $this->seoDetails->whereIn('page_name', $footerMenu);
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.seo-detail.index', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $this->seoDetail = SeoDetail::findOrFail($id);
        $this->lang = $this->seoDetail->language;

        return view('super-admin.front-setting.seo-detail.ajax.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $seoDetail = SeoDetail::findOrFail($id);
        $seoDetail->update($request->except('og_image'));

        $this->langCode = $seoDetail->language->language_code;

        if ($request->hasFile('og_image')) {
            Files::deleteFile($seoDetail->og_image, 'front/seo-detail');
            $seoDetail->og_image = Files::uploadLocalOrS3($request->og_image, 'front/seo-detail');
            $seoDetail->save();
        }

        return Reply::redirect(route('superadmin.front-settings.seo-detail.index', $this->langCode));
    }

}
