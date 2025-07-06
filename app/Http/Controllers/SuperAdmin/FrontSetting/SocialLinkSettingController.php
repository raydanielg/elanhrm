<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\SuperAdmin\FrontDetail;
use Illuminate\Support\Arr;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdateFrontSettings;
use App\Models\GlobalSetting;

class SocialLinkSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.frontCms.socialLinks';
        $this->activeSettingMenu = 'social_link';

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
    public function socialLink()
    {
        $this->pageTitle = 'superadmin.frontCms.socialLinks';

        $this->frontDetail = FrontDetail::first();

        return view('super-admin.front-setting.social-links.index', $this->data);
    }

    public function socialLinkUpdate(UpdateFrontSettings $request)
    {
        $setting = FrontDetail::findOrFail($request->linkId);

        $links = [];

        foreach ($request->social_links as $name => $value) {
            $link_details = [];
            $link_details = Arr::add($link_details, 'name', $name);
            $link_details = Arr::add($link_details, 'link', $value);
            array_push($links, $link_details);
        }

        $setting->social_links = json_encode($links);

        $setting->save();

        return Reply::success(__('messages.updateSuccess'));
    }

}
