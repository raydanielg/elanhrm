<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\FrontClients;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\ClientSettings\StoreRequest;
use App\Http\Requests\SuperAdmin\ClientSettings\UpdateRequest;
use App\Models\GlobalSetting;

class ClientSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.clientSetting';
        $this->activeSettingMenu = 'client_settings';

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
        $this->activeSettingMenu = 'client_settings';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();


        $this->trFrontDetail = TrFrontDetail::select('client_title', 'client_detail', 'language_setting_id')->where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = TrFrontDetail::select('language_setting_id')->whereNotNull('client_title')->get()->toArray();

        $this->clients = FrontClients::with('language:id,language_name')->where('language_setting_id', $this->lang->id)->get();

        $this->activeTab = $this->lang->language_code;
        $this->view = 'super-admin.front-setting.client-settings.ajax.lang';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.client-settings.index', $this->data);
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

        return view('super-admin.front-setting.client-settings.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $frontClients = new FrontClients();

        $frontClients->language_setting_id = $request->current_language_id;
        $frontClients->title = $request->title;

        if ($request->hasFile('image')) {
            $frontClients->image = Files::uploadLocalOrS3($request->image, 'front/client');
        }

        $frontClients->save();

        $this->clients = FrontClients::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

        $html = view('super-admin.front-setting.client-settings.client-setting-data', $this->data)->render();

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
        $this->client = FrontClients::findOrFail($id);
        $this->lang = $request->lang;
        $this->langCode = LanguageSetting::where('id', $this->lang)->first();

        return view('super-admin.front-setting.client-settings.edit', $this->data);
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
        $frontClients = FrontClients::findOrFail($id);

        $frontClients->language_setting_id = $request->current_language_id;
        $frontClients->title = $request->title;

        if ($request->image_delete == 'yes') {
            Files::deleteFile($frontClients->image, 'front/client');
            $frontClients->image = null;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($frontClients->image, 'front/client');
            $frontClients->image = Files::uploadLocalOrS3($request->image, 'front/client');
        }

        $frontClients->save();

        $this->clients = FrontClients::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

        $html = view('super-admin.front-setting.client-settings.client-setting-data', $this->data)->render();

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
        FrontClients::destroy($id);

        $this->clients = FrontClients::with('language:id,language_name')->where('language_setting_id', $request->current_language_id)->get();

        $html = view('super-admin.front-setting.client-settings.client-setting-data', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['html' => $html]);

    }

    public function updateLang(Request $request)
    {
        $row = TrFrontDetail::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        $data = [
            'client_title' => $request->title,
            'client_detail' => $request->detail,
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
