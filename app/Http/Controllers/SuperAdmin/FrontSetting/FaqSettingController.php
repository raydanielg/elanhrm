<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\FrontFaq;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FaqSettings\StoreRequest;
use App\Http\Requests\SuperAdmin\FaqSettings\UpdateRequest;
use App\Models\GlobalSetting;

class FaqSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.faqSetting';
        $this->activeSettingMenu = 'faq_settings';

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
        $this->activeSettingMenu = 'faq_settings';

        $this->view = 'super-admin.front-setting.faq-settings.ajax.lang';

        $this->lang = LanguageSetting::where('language_code', $lang)->firstOrFail();
        $this->activeTab = $this->lang->language_code;

        $this->trFrontDetail = TrFrontDetail::select('faq_title', 'language_setting_id')->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('faq_title')->get()->toArray();

        $this->faqs = FrontFaq::with('language:id,language_name')->where('language_setting_id', $this->lang->id)->get();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.faq-settings.index', $this->data);
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

        return view('super-admin.front-setting.faq-settings.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $frontFaq = new FrontFaq();

        $frontFaq->language_setting_id = $request->current_language_id;
        $frontFaq->question = $request->question;
        $frontFaq->answer = $request->answer;
        $frontFaq->save();

        $this->faqs = FrontFaq::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();
        $html = view('super-admin.front-setting.faq-settings.faq-data', $this->data)->render();

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
        $this->faq = FrontFaq::findOrFail($id);
        $this->lang = $request->lang;
        $this->langCode = LanguageSetting::where('id', $this->lang)->first();

        return view('super-admin.front-setting.faq-settings.edit', $this->data);
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
        $frontFaq = FrontFaq::findOrFail($id);

        $frontFaq->language_setting_id = $request->current_language_id;
        $frontFaq->question = $request->question;
        $frontFaq->answer = $request->answer;
        $frontFaq->save();

        $this->faqs = FrontFaq::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();
        $html = view('super-admin.front-setting.faq-settings.faq-data', $this->data)->render();

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
        FrontFaq::destroy($id);

        $this->faqs = FrontFaq::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();
        $html = view('super-admin.front-setting.faq-settings.faq-data', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['html' => $html]);

    }

    public function updateLang(Request $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = [
            'faq_title' => $request->title,
            'language_setting_id' => $request->language_setting_id,
        ];

        if (!is_null($row)) {
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

}
