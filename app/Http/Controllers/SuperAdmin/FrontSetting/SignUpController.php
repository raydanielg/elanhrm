<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\SignUpSetting;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\Admin\SignUpSetting\SignUpSettingRequest;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\UserAuth;
use App\Models\User;

class SignUpController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.signUpSetting';
        $this->activeSettingMenu = 'sign_up_settings';

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_front_settings'));

        $this->registrationStatus = GlobalSetting::first();
        $this->activeSettingMenu = 'sign_up_settings';

        $this->view = 'super-admin.front-setting.sign-up-setting.ajax.lang';

        $lang = (request()->lang) ? request()->lang : 'en';

        $this->lang = LanguageSetting::where('language_code', $lang)->first();
        $this->activeTab = $this->lang->language_code;

        $this->signUpSetting = SignUpSetting::where('language_setting_id', $this->lang->id)->first();
        $this->allLangTranslation = SignUpSetting::select('language_setting_id')->whereNotNull('message')->get()->toArray();

        $this->frontDetail = FrontDetail::first();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'language_setting_id' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.front-setting.sign-up-setting.index', $this->data);
    }

    // @codingStandardsIgnoreLine
    public function update(SignUpSettingRequest $request, $id)
    {
        abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_front_settings'));

        $registration = GlobalSetting::first();
        $registration->registration_open = $request->registration_open ? 1 : 0;
        $registration->enable_register = $request->enable_register ? 1 : 0;
        $registration->sign_up_terms = ($request->sign_up_terms == 'yes') ? 'yes' : 'no';
        $registration->terms_link = ($request->sign_up_terms == 'yes') ? $request->terms_link : null;
        $registration->save();

        $setting = SignUpSetting::where('language_setting_id', $request->language_setting_id == 0 ? null : $request->language_setting_id)->first();

        if (!$setting) {
            $setting = new SignUpSetting();
        }

        $setting->language_setting_id = $request->language_setting_id == 0 ? null : $request->language_setting_id;
        $setting->message = $request->message;
        $setting->save();

        $frontSetting = FrontDetail::first();
        $frontSetting->sign_in_show = ($request->sign_in_show == 'yes') ? 'yes' : 'no';
        $frontSetting->save();

        cache()->forget('global_setting');

        return Reply::successWithData(__('messages.updateSuccess'), [
            'data' => $request->message,
            'lang' => $setting->language->language_code
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $emailOtp = implode('', $request->email_otp);

        $user = UserAuth::findOrFail(user()->user_auth_id);

        foreach ($request->email_otp as $otp) {
            if ($otp == '') {
                return Reply::error('Invalid verification  code.');
            }
        }

        if ($user->email_verification_code != $emailOtp || ($user->email_code_expires_at && $user->email_code_expires_at->isPast())) {
            return Reply::error('Invalid verification  code.');
        }

        $user->email_verification_code = null;
        $user->email_code_expires_at = null;
        $user->email_verified_at = now();
        $user->saveQuietly();

        User::where('user_auth_id', user()->user_auth_id)->update(
            ['admin_approval' => 1]
        );

        return Reply::redirect(route('dashboard'), __('superadmin.emailVerificationCode.verifiedSuccess'));

    }

}
