<?php

namespace App\Http\Controllers\SuperAdmin\FrontSetting;

use App\Helper\Reply;
use App\Models\SuperAdmin\FrontWidget;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FrontWidget\StoreRequest;
use App\Models\GlobalSetting;

class FrontWidgetController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.frontWidgets';
        $this->activeSettingMenu = 'front_widgets';

        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin('manage_superadmin_front_settings'));

            return $next($request);
        });
    }

    public function index()
    {
        $this->frontWidgets = FrontWidget::all();

        return view('super-admin.front-setting.front-widget.index', $this->data);
    }

    public function create()
    {
        return view('super-admin.front-setting.front-widget.create', $this->data);
    }

    public function store(StoreRequest $request)
    {
        FrontWidget::create(
            [
                'name' => $request->name,
                'header_script' => $request->header_script,
                'footer_script' => $request->footer_script
            ]
        );

        return Reply::redirect(route('superadmin.front-settings.front-widgets.index'), 'messages.recordSaved');
    }

    public function edit($id)
    {
        $this->frontWidget = FrontWidget::find($id);
        return view('super-admin.front-setting.front-widget.edit', $this->data);
    }

    public function update(StoreRequest $request, $id)
    {
        $frontWidget = FrontWidget::find($id);
        $frontWidget->update(
            [
                'name' => $request->name,
                'header_script' => $request->header_script,
                'footer_script' => $request->footer_script
            ]
        );

        return Reply::redirect(route('superadmin.front-settings.front-widgets.index'), 'messages.updateSuccess');
    }

    public function destroy($id)
    {
        FrontWidget::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

}
