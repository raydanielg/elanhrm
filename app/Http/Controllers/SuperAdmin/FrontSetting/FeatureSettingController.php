<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Feature;
use App\Models\SuperAdmin\FrontFeature;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FeatureSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FeatureSetting\UpdateRequest;
use App\Models\GlobalSetting;

class FeatureSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.frontFeatureSettings';
        $this->activeSettingMenu = 'features';

        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_front_settings'));

            return $next($request);
        });
    }

    public function index()
    {
        $this->pageTitle = 'superadmin.menu.features';
        $this->activeSettingMenu = 'feature';
        $tab = request('tab');

        switch ($tab) {
        case 'settings':
            $this->featureSettings = FrontFeature::with('language:id,language_name', 'features', 'features.language')->get();
            $this->view = 'super-admin.front-setting.feature-setting.feature-setting-data';
            break;
        default:
            $this->features = Feature::with('language:id,language_name')->where('type', request('tab'))->whereNull('front_feature_id')->get();
            $this->view = 'super-admin.front-setting.feature-setting.feature-data';
            break;
        }

        $this->type = $tab;

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->type]);
        }

        return view('super-admin.front-setting.feature-setting.index', $this->data);
    }

    public function create(Request $request)
    {
        $this->featureId = $request->featureSettingId;
        $this->type = $request->type;
        return view('super-admin.front-setting.feature-setting.create', $this->data);
    }

    public function store(StoreRequest $request)
    {
        if($request->type == 'settings'){
            $feature = new FrontFeature();
        }
        else {
            $feature = new Feature();
            $feature->type = $request->type;

            if($request->featureId) {
                $feature->front_feature_id = $request->featureId;
            }

            if ($request->has('icon')) {
                $feature->icon = $request->icon;
            }

            if ($request->hasFile('image')) {
                $feature->image = Files::uploadLocalOrS3($request->image, 'front/feature');
            }
        }

        $feature->language_setting_id = $request->language == 0 ? null : $request->language;
        $feature->title = $request->title;
        $feature->description = $request->description;
        $feature->save();

        $this->type = $request->type;

        if($request->type == 'settings' || $request->featureId != null) {
            $this->featureSettings = FrontFeature::with('language:id,language_name', 'features', 'features.language')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-setting-data', $this->data)->render();
        }
        else {
            $this->features = Feature::with('language:id,language_name')->where('type', $request->type)->whereNull('front_feature_id')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-data', $this->data)->render();
        }

        return Reply::successWithData(__('messages.recordSaved'), ['html' => $html]);
    }

    public function edit(Request $request, $id)
    {
        if($request->type == 'settings'){
            $this->feature = FrontFeature::with('features')->findOrFail($id);
        }
        else{
            $this->feature = Feature::findOrFail($id);
        }

        $this->type = $request->type;

        return view('super-admin.front-setting.feature-setting.edit', $this->data);

    }

    public function update(UpdateRequest $request, $id)
    {
        if($request->type == 'settings'){
            $feature = FrontFeature::findOrFail($id);
        }
        else {
            $feature = Feature::findOrFail($id);
            $feature->type = $request->type;

            if ($request->has('icon')) {
                $feature->icon = $request->icon;
            }

            if ($request->image_delete == 'yes') {
                Files::deleteFile($feature->image, 'front/feature');
                $feature->image = null;
            }

            if ($request->hasFile('image')) {
                Files::deleteFile($feature->image, 'front/feature');
                $feature->image = Files::uploadLocalOrS3($request->image, 'front/feature');
            }
        }

        $feature->language_setting_id = $request->language == 0 ? null : $request->language;
        $feature->title = $request->title;
        $feature->description = $request->description;
        $feature->save();

        $this->type = $request->type;

        if($request->type == 'settings' || $feature->front_feature_id != null) {
            $this->featureSettings = FrontFeature::with('language:id,language_name', 'features', 'features.language')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-setting-data', $this->data)->render();
        }
        else {
            $this->features = Feature::with('language:id,language_name')->where('type', $request->type)->whereNull('front_feature_id')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-data', $this->data)->render();
        }

        return Reply::successWithData(__('messages.recordSaved'), ['html' => $html]);

    }

    public function destroy(Request $request, $id)
    {
        if($request->type == 'settings'){
            Feature::where('front_feature_id', $id)->delete();
            FrontFeature::destroy($id);
        }
        else {
            Feature::destroy($id);
        }

        $this->type = $request->type;

        if($request->type == 'settings' || $request->featureSettingId != null) {
            $this->featureSettings = FrontFeature::with('language:id,language_name', 'features', 'features.language')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-setting-data', $this->data)->render();
        }
        else {
            $this->features = Feature::with('language:id,language_name')->where('type', $request->type)->whereNull('front_feature_id')->get();
            $html = view('super-admin.front-setting.feature-setting.feature-data', $this->data)->render();
        }

        return Reply::successWithData(__('messages.deleteSuccess'), ['html' => $html]);
    }

}
