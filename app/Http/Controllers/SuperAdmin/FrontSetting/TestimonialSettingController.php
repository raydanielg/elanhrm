<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Testimonials;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\TestimonialSettings\StoreRequest;
use App\Http\Requests\SuperAdmin\TestimonialSettings\UpdateRequest;
use App\Http\Requests\SuperAdmin\TestimonialSettings\TitleStoreUpdateRequest;
use App\Models\GlobalSetting;

class TestimonialSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.testimonialSetting';
        $this->activeSettingMenu = 'testimonial_settings';

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
    public function index()
    {
        $this->pageTitle = 'superadmin.menu.testimonialSetting';
        $this->testimonials = Testimonials::with('language:id,language_name')->get();

        $this->view = 'super-admin.front-setting.testimonial-settings.testimonial';
        $tab = request('tab');


        switch ($tab) {
        case 'title':
            $this->titles = TrFrontDetail::with('language')->select('id', 'testimonial_title', 'language_setting_id')->get();
            $this->view = 'super-admin.front-setting.testimonial-settings.translation';
            break;
        default:
            $this->view = 'super-admin.front-setting.testimonial-settings.testimonial';
            break;
        }

        $this->activeTab = $tab ?: 'setting';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('super-admin.front-setting.testimonial-settings.index', $this->data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('super-admin.front-setting.testimonial-settings.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $testimonial = new Testimonials();
        $testimonial->language_setting_id = $request->language == 0 ? null : $request->language;
        $testimonial->name = $request->name;
        $testimonial->comment = $request->comment;
        $testimonial->rating = $request->rating;

        $testimonial->save();

        return Reply::success(__('messages.recordSaved'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->testimonial = Testimonials::findOrFail($id);

        return view('super-admin.front-setting.testimonial-settings.ajax.edit', $this->data);
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
        $testimonial = Testimonials::findOrFail($id);

        $testimonial->language_setting_id = $request->language == 0 ? null : $request->language;
        $testimonial->name = $request->name;
        $testimonial->comment = $request->comment;
        $testimonial->rating = $request->rating;
        $testimonial->save();

        return Reply::redirect(route('superadmin.front-settings.testimonial-settings.index'), 'messages.recordSaved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Testimonials::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function createTestimonialTitle()
    {
        return view('super-admin.front-setting.testimonial-settings.ajax.create-title', $this->data);
    }

    public function storeOrUpdateTestimonialTitle(TitleStoreUpdateRequest $request)
    {
        TrFrontDetail::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'language_setting_id' => $request->language,
                'testimonial_title' => $request->testimonial_title
            ]
        );

        return Reply::success(__('messages.recordSaved'));
    }

    public function editTestimonialTitle($id)
    {
        $this->testimonialTitle = TrFrontDetail::with('language')->select('id', 'testimonial_title', 'language_setting_id')->findOrFail($id);

        return view('super-admin.front-setting.testimonial-settings.ajax.edit-title', $this->data);
    }

}
