<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\SeoDetail;
use App\Models\SuperAdmin\FooterMenu;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FooterSetting\CtaRequest;
use App\Http\Requests\SuperAdmin\FooterSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FooterSetting\UpdateRequest;
use App\Http\Requests\SuperAdmin\FooterSetting\FooterTextRequest;
use App\Models\GlobalSetting;

class FooterSettingController extends AccountBaseController
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
    public function index($lang = 'en')
    {
        $this->pageTitle = 'superadmin.menu.footerSetting';
        $this->activeSettingMenu = 'footer_setting';
        $this->view = 'super-admin.front-setting.footer-setting.ajax.lang';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::select('footer_copyright_text', 'language_setting_id')->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('footer_copyright_text')->get()->toArray();

        $this->footer = FooterMenu::with('language:id,language_name,flag_code')->where('language_setting_id', $this->lang->id)->get();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.footer-setting.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->lang = $request->lang;
        $this->langCode = LanguageSetting::where('id', $this->lang)->first();

        return view('super-admin.front-setting.footer-setting.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $footer = new FooterMenu();

        $footer->language_setting_id = $request->current_language_id;
        $footer->name = $request->title;
        $footer->slug = $request->slug;

        if ($request->content == 'link') {
            $footer->description = null;
            $footer->external_link = $request->external_link;
        }
        else {
            $footer->description = str_replace('<p><br></p>', '', $request->description);
            $footer->external_link = null;
        }

        $footer->status = $request->status;
        $footer->type = $request->type;
        $footer->private = ($request->private == 'yes') ? 1 : 0;
        $footer->save();

        SeoDetail::updateOrCreate(['page_name' => $footer->slug],
            [
                'language_setting_id' => $footer->language_setting_id,
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_author' => $request->seo_author,
                'seo_keywords' => $request->seo_keywords
            ]
        );

        $this->footer = FooterMenu::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

        $html = view('super-admin.front-setting.footer-setting.footer-data', $this->data)->render();

        return Reply::successWithData(__('messages.recordSaved'), ['html' => $html]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->footer = FooterMenu::findOrFail($id);
        $this->seoDetail = SeoDetail::where('page_name', $this->footer->slug)->first();
        $this->lang = $request->lang;
        $this->langCode = LanguageSetting::where('id', $this->lang)->first();

        return view('super-admin.front-setting.footer-setting.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $footer = FooterMenu::findOrFail($id);

        $footer->language_setting_id = $request->current_language_id;
        $footer->name = $request->title;

        if ($request->content == 'link') {
            $footer->description = null;
            $footer->external_link = $request->external_link;
        }
        else {
            $footer->description = $request->description;
            $footer->external_link = null;
        }

        $footer->status = $request->status;
        $footer->type = $request->type;
        $footer->private = ($request->private == 'yes') ? 1 : 0;
        $footer->save();

        SeoDetail::updateOrCreate(
            ['page_name' => $footer->slug],
            [
                'language_setting_id' => $request->language == 0 ? null : $request->language,
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_author' => $request->seo_author,
                'seo_keywords' => $request->seo_keywords
            ]
        );

        $this->footer = FooterMenu::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

        $html = view('super-admin.front-setting.footer-setting.footer-data', $this->data)->render();

        return Reply::successWithData(__('messages.recordSaved'), ['html' => $html]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $footerMenu = FooterMenu::select('id', 'language_setting_id', 'slug')->where('id', $id)->first();

            if ($footerMenu) {
                SeoDetail::select('id', 'page_name')->where('page_name', $footerMenu->slug)->delete();
            }

            $footerMenu->delete();

            $this->footer = FooterMenu::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

            $html = view('super-admin.front-setting.footer-setting.footer-data', $this->data)->render();

            return Reply::successWithData(__('messages.deleteSuccess'), ['html' => $html]);

        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }

    }

    public function updateLang(FooterTextRequest $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = [
            'footer_copyright_text' => $request->footer_copyright_text,
            'language_setting_id' => $request->language_setting_id
        ];

        if ($row) {
            $row->update($data);
        }
        else {
            $row = TrFrontDetail::create($data);
        }

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->footer_copyright_text,
            'lang' => $row->language->language_code
        ]);
    }

    public function ctaLang($lang = 'en')
    {
        $this->pageTitle = 'superadmin.menu.ctaSetting';
        $this->activeSettingMenu = 'cta_settings';
        $this->view = 'super-admin.front-setting.footer-setting.ajax.lang-cta';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::select('cta_title', 'cta_detail', 'language_setting_id')->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('cta_title')->get()->toArray();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.footer-setting.translation-cta', $this->data);
    }

    public function updateCtaLang(CtaRequest $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = [
            'language_setting_id' => $request->language_setting_id,
            'cta_title' => $request->title,
            'cta_detail' => $request->detail
        ];

        if ($row) {
            $row->update($data);
        }
        else {
            $row = TrFrontDetail::create($data);
        }

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->title,
            'lang' => $row->language->language_code
        ]);
    }

    public function generateSlug(Request $request)
    {
        $slug = Str::slug($request->title, '-');

        $activeLang = LanguageSetting::where('status', 'enabled')->first();

        $slug = $this->getUniqueSlug($slug, $activeLang->id);

        return Reply::dataOnly(['slug' => $slug]);
    }

    private function getUniqueSlug($slug, $activeLangId, $slugCount = null)
    {
        if($slugCount){
            // remove last - and increment count
            $slug = str($slug)->beforeLast('-') . '-' . ($slugCount + 1);
        }

        $count = FooterMenu::where('slug', $slug)->where('language_setting_id', $activeLangId)->count();

        if ($count == 0) {
            return $slug;
        }

        if ($count > 0) {
            $slug = $slug . '-' . $count;
        }

        return self::getUniqueSlug($slug, $activeLangId, $count);
    }

}
