<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Services\Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GoogleAuthController extends Controller
{

    public function index(Request $request, Google $google)
    {

        if (!$request->code) {
            /** @phpstan-ignore-next-line */
            return redirect($google->createAuthUrl());
        }

        // WORKSUITESAAS
        if ($request->state) {
            /** @phpstan-ignore-next-line */
            $google->authenticate($request->code);
            $account = $google->service('Oauth2')->userinfo->get();
            return redirect($request->state . '?google_id=' . $account->id . '&userName=' . $account->name . '&access_token=' . json_encode($google->getAccessToken()) . '&code=' . $request->code);
        }

        if(isWorksuite()) {
            /** @phpstan-ignore-next-line */
            $google->authenticate($request->code);
            $account = $google->service('Oauth2')->userinfo->get();
        }

        $googleAccount = companyOrGlobalSetting();

        if (empty($googleAccount->user_id) && empty($googleAccount->google_id) && empty($googleAccount->name) && empty($googleAccount->token)) {
            Session::flash('message', __('messages.googleCalendar.verifiedSuccess'));
        }
        else {
            Session::flash('message', __('messages.googleCalendar.updatedSuccess'));
        }

        $googleAccount->google_calendar_verification_status = 'verified';

        if(isWorksuite()){
            $googleAccount->google_id = $account->id;
            $googleAccount->name = $account->name;
            /** @phpstan-ignore-next-line */
            $googleAccount->token = $google->getAccessToken();
        }
        else{
            // WORKSUITESAAS
            $googleAccount->google_id = $request->google_id;
            $googleAccount->name = $request->userName;
            /** @phpstan-ignore-next-line */
            $googleAccount->token = $request->access_token;
        }

        $googleAccount->update();

        cache()->forget('global_setting');

        return redirect()->route('google-calendar-settings.index');
    }

    public function destroy()
    {
        $googleAccount = companyOrGlobalSetting();
        $googleAccount->google_calendar_verification_status = 'non_verified';
        $googleAccount->google_id = '';
        $googleAccount->name = '';
        $googleAccount->token = '';
        $googleAccount->save();

        session()->forget('company_setting');
        session()->forget('company');
        cache()->forget('global_setting');

        return Reply::success(__('messages.googleCalendar.removedSuccess'));
    }

}
