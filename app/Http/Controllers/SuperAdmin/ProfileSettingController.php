<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;

class ProfileSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->activeSettingMenu = 'profile_settings';
    }

    public function index()
    {
        $this->user = user();
        $this->countries = countries();
        $this->view = 'super-admin.profile.ajax.profile';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.profile.index', $this->data);
    }

}
