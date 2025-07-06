<?php

namespace App\Http\Controllers\SuperAdmin;

use App\DataTables\SuperAdmin\PackageDataTable;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\Packages\StoreRequest;
use App\Http\Requests\SuperAdmin\Packages\UpdateRequest;
use App\Models\Company;
use App\Models\GlobalSetting;
use App\Models\Module;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\PackageSetting;

class PackageController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.packages';
        $this->global = global_setting();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PackageDataTable $dataTable)
    {
        $this->viewPermission = user()->permission('view_packages');

        abort_403(!($this->viewPermission == 'all'));

        return $dataTable->render('super-admin.packages.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_packages');

        abort_403(!($this->addPermission == 'all'));

        $this->global = GlobalSetting::first();
        $this->paymentGateway = GlobalPaymentGatewayCredentials::first();
        $this->pageTitle = __('superadmin.packages.create');
        $this->position = Package::count();


        $this->packageModules = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->get();

        $this->currencies = GlobalCurrency::all();

        $this->packageCount = Package::select('id', 'sort')->whereNot('default', 'trial')->orderBy('sort', 'asc')->count();

        if (request()->ajax()) {
            $html = view('super-admin.packages.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.packages.ajax.create';

        return view('super-admin.packages.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->addPermission = user()->permission('add_packages');

        abort_403(!($this->addPermission == 'all'));

        if ($request->module_in_package == null) {
            return Reply::error(__('superadmin.messages.moduleBlank'));
        }

        if ($request->has('is_recommended') && $request->is_recommended == 'on') {
            Package::where('is_recommended', 1)->update(['is_recommended' => 1]);
        }

        $data = $this->modifyRequest($request);
        Package::create($data);

        return Reply::redirect(route('superadmin.packages.index'), __('messages.packageCreated'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_packages');

        abort_403(!($this->editPermission == 'all'));

        $this->pageTitle = __('superadmin.packages.edit');
        $this->package = Package::findOrFail($id);

        $this->packageModules = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->get();

        $this->paymentGateway = GlobalPaymentGatewayCredentials::first();
        $this->currencies = GlobalCurrency::all();

        if ($this->package->default == 'trial') {
            $this->trial = PackageSetting::first();
        }

        $this->packageCount = Package::select('id', 'sort')->whereNot('default', 'trial')->orderBy('sort', 'asc')->count();

        if (request()->ajax()) {
            $html = view('super-admin.packages.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.packages.ajax.edit';

        return view('super-admin.packages.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_packages');

        abort_403(!($this->editPermission == 'all'));

        if ($request->module_in_package == null) {
            return Reply::error(__('superadmin.messages.moduleBlank'));
        }

        if ($request->has('is_recommended') && $request->is_recommended == 'on') {
            Package::where('is_recommended', 1)->update(['is_recommended' => 1]);
        }

        $package = Package::with('companies')->find($id);
        $data = $this->modifyRequest($request);

        $package->update($data);

        // Update if trial package is modified
        $this->updateTrialPackage($package, $request);

        return Reply::redirect(route('superadmin.packages.index'), __('messages.updateSuccess'));
    }

    private function updateTrialPackage($package, $request)
    {
        if ($package->default == 'trial') {
            $setting = PackageSetting::first();
            $setting->no_of_days = $request->no_of_days;
            $setting->notification_before = $request->notification_before;
            $setting->trial_message = $request->trial_message;
            $setting->status = $request->status;
            $setting->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_packages');
        abort_403(!($this->deletePermission == 'all'));

        $package = Package::findOrFail($id);

        if ($package->default != 'no') {
            return Reply::error(__('superadmin.packages.defaultPackageCannotDelete'));
        }

        $companies = Company::where('package_id', $id)->get();

        if ($companies) {
            $defaultPackage = Package::where('default', 'yes')->first();

            if ($defaultPackage) {
                foreach ($companies as $company) {
                    $company->package_id = $defaultPackage->id;
                    $company->save();

                    $this->updateSubscription($company, $defaultPackage);
                }
            }
        }

        $package->delete();

        return Reply::success('messages.deleteSuccess');
    }

    private function modifyRequest($request)
    {

        $data = $request->all();
        $data['module_in_package'] = json_encode($request->module_in_package);
        $data['is_private'] = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $data['is_recommended'] = $request->has('is_recommended') && $request->is_recommended == 'on' ? 1 : 0;

        if ($request->package == 'lifetime') {
            $data['monthly_status'] = 0;
            $data['annual_status'] = 0;
        } else {
            $data['monthly_status'] = $request->has('monthly_status') && $request->monthly_status == 'true' ? 1 : 0;
            $data['annual_status'] = $request->has('annual_status') && $request->annual_status == 'true' ? 1 : 0;
        }

        $data['sort'] = $request->sort;
        $data['currency_id'] = $request->currency_id;

        if ($request->has('package_type')) {
            $data['is_free'] = (($request->package_type == 'free') ? 1 : 0);
            $data['price'] = (($request->package == 'lifetime') ? $request->price : 0);

            if ($request->package == 'lifetime') {
                $data['default'] = 'lifetime';
            }

            if ($request->package_type == 'free') {
                $data['monthly_price'] = 0;
                $data['annual_price'] = 0;
            }
        }
        return $data;
    }

    public function updateSubscription(Company $company, Package $package)
    {
        $packageType = $package->annual_status ? 'annual' : 'monthly';
        $currencyId = $package->currency_id ?: global_setting()->currency_id;
        $planExpireDate = $company->licence_expire_on;

        if (!$planExpireDate) {
            $planExpireDate = $packageType == 'annual' ? now()->addYear() : now()->addMonth();
        }

        GlobalSubscription::where('company_id', $company->id)
            ->where('subscription_status', 'active')
            ->update(['subscription_status' => 'inactive']);

        $subscription = new GlobalSubscription();
        $subscription->company_id = $company->id;
        $subscription->package_id = $package->id;
        $subscription->currency_id = $currencyId;
        $subscription->package_type = $packageType;
        $subscription->quantity = 1;
        $subscription->gateway_name = 'offline';
        $subscription->subscription_status = 'active';
        $subscription->subscribed_on_date = now();
        $subscription->ends_at = $planExpireDate;
        $subscription->transaction_id = str(str()->random(15))->upper();
        $subscription->save();

        $offlineInvoice = new GlobalInvoice();
        $offlineInvoice->global_subscription_id = $subscription->id;
        $offlineInvoice->company_id = $company->id;
        $offlineInvoice->currency_id = $currencyId;
        $offlineInvoice->package_id = $company->package_id;
        $offlineInvoice->package_type = $packageType;
        $offlineInvoice->total = 0.00;
        $offlineInvoice->pay_date = now();
        $offlineInvoice->next_pay_date = $planExpireDate;
        $offlineInvoice->gateway_name = 'offline';
        $offlineInvoice->transaction_id = $subscription->transaction_id;
        $offlineInvoice->save();
    }
}
