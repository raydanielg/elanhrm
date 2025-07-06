<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Company;
use App\Models\GlobalSetting;
use Illuminate\Support\Carbon;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\OfflinePlanChange;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Http\Controllers\AccountBaseController;
use App\DataTables\SuperAdmin\OfflinePlanChangeDataTable;
use App\Http\Requests\SuperAdmin\Billing\OfflinePlanChangeRequest;

class OfflinePlanChangeController extends AccountBaseController
{

    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('superadmin.menu.offlineRequest');
        $this->pageIcon = 'icon-settings';

        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin());

            return $next($request);
        });
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OfflinePlanChangeDataTable $dataTable)
    {
        $this->viewPermission = user()->permission('view_request');
        abort_403(!($this->viewPermission == 'all'));

        $this->global = GlobalSetting::first();
        $this->totalRequest = OfflinePlanChange::count();

        return $dataTable->render('super-admin.offline-plan-change.index', $this->data);
    }

    public function show($id)
    {
        $this->viewPermission = user()->permission('view_request');
        abort_403(!($this->viewPermission == 'all'));

        $this->offlinePlanChange = OfflinePlanChange::with('company', 'package', 'offlineMethod')->findOrFail($id);
        $this->pageTitle = $this->offlinePlanChange->company->company_name;
        $this->view = 'super-admin.offline-plan-change.ajax.show';


        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('super-admin.offline-plan-change.show', $this->data);
    }

    public function confirmChangePlan($id, $status)
    {

        abort_403(user()->permission('accept_reject_request') !== 'all');

        $this->offlinePlanChange = OfflinePlanChange::with('company','package')->findOrFail($id);
        $this->pageTitle = $this->offlinePlanChange->company->company_name;

        $view = ($status == 'verified') ? 'super-admin.offline-plan-change.ajax.verify' : 'super-admin.offline-plan-change.ajax.reject';

        return view($view, $this->data);
    }

    public function changePlan(OfflinePlanChangeRequest $request)
    {
        $this->acceptPermission = user()->permission('accept_reject_request');
        abort_403(!($this->acceptPermission == 'all'));

        $offlinePlanChange = OfflinePlanChange::with('package')->findOrFail($request->id);
        if ($request->status == 'verified') {
            GlobalSubscription::where('company_id', $offlinePlanChange->company_id)->update(['subscription_status' => 'inactive']);

            $offlinePlanChange->pay_date = Carbon::createFromFormat($this->global->date_format, $request->pay_date)->format('Y-m-d');

            $offlinePlanChange->next_pay_date = ($offlinePlanChange->package->package == 'lifetime') ? null : Carbon::parse($request->next_pay_date)->format('Y-m-d');
            $offlinePlanChange->status = 'verified';

            $subscription = new GlobalSubscription();
            $subscription->company_id = $offlinePlanChange->company_id;
            $subscription->package_id = $offlinePlanChange->package_id;
            $subscription->package_type = $offlinePlanChange->package_type;
            $subscription->gateway_name = 'offline';
            $subscription->subscription_status = 'active';
            $subscription->subscribed_on_date = Carbon::createFromFormat($this->global->date_format, $request->pay_date)->format('Y-m-d');
            $subscription->transaction_id = str(str()->random(15))->upper();
            $subscription->save();

            $invoice = new GlobalInvoice();
            $invoice->company_id = $offlinePlanChange->company_id;
            $invoice->global_subscription_id = $subscription->id;
            $invoice->package_id = $offlinePlanChange->package_id;
            $invoice->currency_id = $offlinePlanChange->package->currency_id;
            $invoice->offline_method_id = $offlinePlanChange->offline_method_id;
            $invoice->package_type = $offlinePlanChange->package_type;
            $invoice->total = $offlinePlanChange->amount;
            $invoice->gateway_name = 'offline';
            $invoice->status = 'active';
            $invoice->pay_date = Carbon::createFromFormat($this->global->date_format, $request->pay_date)->format('Y-m-d');
            $invoice->next_pay_date = ($offlinePlanChange->package->package == 'lifetime') ? null : Carbon::createFromFormat($this->global->date_format, $request->next_pay_date)->format('Y-m-d');
            $invoice->transaction_id = $subscription->transaction_id;
            $invoice->save();

            // Change company package
            $company = Company::find($offlinePlanChange->company_id);
            $company->package_id = $offlinePlanChange->package_id;
            $company->package_type = $offlinePlanChange->package_type;
            $company->licence_expire_on = ($offlinePlanChange->package->package == 'lifetime') ? null : Carbon::createFromFormat($this->global->date_format, $request->next_pay_date)->format('Y-m-d');

            $company->save();
        }
        elseif ($request->status == 'rejected') {
            $offlinePlanChange->remark = $request->remark;
            $offlinePlanChange->status = 'rejected';
        }

        // set status of request verified
        $offlinePlanChange->save();

        return Reply::success('messages.updateSuccess');
    }

    /**
     * @param int $id
     * @return string
     */
    public function download($id)
    {
        $file = OfflinePlanChange::whereRaw('md5(id) = ?', $id)->firstOrFail();

        $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
        $filename = $file->name ? $file->name . '.' . $ext : $file->filename;

        try {
            $filePath = public_path(Files::UPLOAD_FOLDER . '/' . OfflinePlanChange::FILE_PATH . '/' . $file->file_name);
            return response()->download($filePath, $filename);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

}
