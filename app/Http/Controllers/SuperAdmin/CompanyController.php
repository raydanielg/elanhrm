<?php

namespace App\Http\Controllers\SuperAdmin;

use App\DataTables\SuperAdmin\CompanyDataTable;
use App\DataTables\SuperAdmin\InvoiceDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\Company\PackageUpdateRequest;
use App\Http\Requests\SuperAdmin\Company\StoreRequest;
use App\Http\Requests\SuperAdmin\Company\UpdateRequest;
use App\Models\Company;
use App\Models\Currency;
use App\Models\EmployeeDetails;
use App\Models\GlobalSetting;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\Role;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\PackageSetting;
use App\Models\UniversalSearch;
use App\Models\User;
use App\Models\UserAuth;
// use App\Models\SuperAdmin\GlobalCurrency;
use App\Notifications\SuperAdmin\CompanyApproved;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use App\Traits\CurrencyExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends AccountBaseController
{

    use CurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('superadmin.menu.companies');

        $this->middleware(function ($request, $next) {
            abort_403(GlobalSetting::validateSuperAdmin());

            return $next($request);
        });
    }

    /**
     * client list
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CompanyDataTable $dataTable)
    {
        $this->viewPermission = user()->permission('view_companies');
        abort_403(!($this->viewPermission == 'all'));

        if (!request()->ajax()) {
            $this->packages = Package::all();
        }

        $this->unapprovedCount = Company::where('approved', 0)->count();

        return $dataTable->render('super-admin.companies.index', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_companies');
        abort_403(!($this->addPermission == 'all'));

        $this->pageTitle = __('superadmin.addCompany');

        $this->timezones = \DateTimeZone::listIdentifiers();
        $this->currencies = GlobalCurrency::all();

        $this->globalCurrenyCode = global_setting()->currency->currency_code;

        $this->fields = [];

        $company = new Company();

        if (!empty($company->getCustomFieldGroupsWithFields())) {
            $this->fields = $company->getCustomFieldGroupsWithFields()->fields;
        }

        $this->view = 'super-admin.companies.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }


        return view('super-admin.companies.create', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $this->addPermission = user()->permission('add_companies');
        abort_403(!($this->addPermission == 'all'));

        DB::beginTransaction();

        $company = $this->storeAndUpdate(new Company(), $request);

        $globalCurrency = GlobalCurrency::findOrFail($request->currency_id);

        $currency = Currency::where('currency_code', $globalCurrency->currency_code)
            ->where('company_id', $company->id)
            ->first();

        if (is_null($currency)) {
            $currency = $this->newCurrency($globalCurrency, $company);
        }

        $company->currency_id = $currency->id;
        $company->save();

        // To add custom fields data
        if ($request->custom_fields_data) {
            $company->updateCustomFieldData($request->custom_fields_data);
        }

        $this->addUser($company, $request);

        DB::commit();

        return Reply::redirect(route('superadmin.companies.index'), __('messages.companyCreated'));
    }

    private function newCurrency($globalCurrency, $company)
    {
        $currency = new Currency();
        $currency->currency_name = $globalCurrency->currency_name;
        $currency->currency_symbol = $globalCurrency->currency_symbol;
        $currency->currency_code = $globalCurrency->currency_code;
        $currency->is_cryptocurrency = $globalCurrency->is_cryptocurrency;
        $currency->usd_price = $globalCurrency->usd_price;
        $currency->company_id = $company->id;
        $currency->save();

        return $currency;
    }

    public function storeAndUpdate(Company $company, $request)
    {
        $company->company_name = $request->company_name;
        $company->app_name = $request->company_name;
        $company->company_email = $request->company_email;
        $company->company_phone = $request->company_phone;
        $company->website = $request->website;
        $company->address = $request->address ?? $company->company_name;
        $company->timezone = $request->timezone;
        $company->locale = $request->locale;
        $company->status = $request->status;

        if ($request->has('approved')) {
            $company->approved = $request->approved;
        }

        if ($request->hasFile('logo')) {
            $company->logo = Files::uploadLocalOrS3($request->logo, 'app-logo');
            $company->light_logo = $company->logo;
        }

        $company->last_updated_by = $this->user->id;

        if (module_enabled('Subdomain')) {
            $company->sub_domain = strtolower($request->sub_domain . $request->domain);
        }

        $company->save();

        $company->defaultAddress->update(['address' => $request->address]);

        return $company;
    }

    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_companies');
        abort_403(!($this->editPermission == 'all'));

        $this->pageTitle = __('app.update') . ' ' . __('superadmin.company');
        $this->company = Company::with('defaultAddress')->findOrFail($id)->withCustomFields();
        $this->company->user = Company::firstActiveAdmin($this->company);
        $this->timezones = \DateTimeZone::listIdentifiers();
        $this->currencies = Currency::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->get();

        $this->fields = [];

        if (!empty($this->company->getCustomFieldGroupsWithFields())) {
            $this->fields = $this->company->getCustomFieldGroupsWithFields()->fields;
        }

        if (request()->ajax()) {
            $html = view('super-admin.companies.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.companies.ajax.edit';

        return view('super-admin.companies.create', $this->data);

    }

    public function update(UpdateRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_companies');
        abort_403(!($this->editPermission == 'all'));

        $company = Company::findOrFail($id);

        DB::beginTransaction();
        $company = $this->storeAndUpdate($company, $request);

        $currency = Currency::withoutGlobalScope(CompanyScope::class)->where('id', $request->currency_id)
            ->where('company_id', $company->id)
            ->first();

        $company->currency_id = $currency->id;
        $company->save();

        // To add custom fields data
        if ($request->custom_fields_data) {
            $company->updateCustomFieldData($request->custom_fields_data);
        }

        DB::commit();

        return Reply::redirect(route('superadmin.companies.index'), __('messages.companyCreated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_companies');
        abort_403(!($this->deletePermission == 'all'));

        Company::where('id', $id)->update(['default_task_status' => null]);
        Company::destroy($id);

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('superadmin.companies.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $this->viewPermission = user()->permission('view_companies');
        abort_403(!($this->viewPermission == 'all'));


        $tab = request('tab');

        switch ($tab) {
        case 'billing':
            $this->company = Company::with('currency', 'package', 'approvalBy')
                ->withCount(['employees', 'fileStorage', 'clients', 'invoices', 'estimates', 'contracts', 'projects', 'tasks', 'leads', 'tickets', 'orders'])
                ->withSum('fileStorage', 'size')
                ->with(['companyAddress' => function ($query) {
                    return $query->where('is_default', 1);
                }])
                ->findOrFail($id);
            return $this->billing();
        case 'headers':
            $this->company = Company::select(['id', 'headers', 'register_ip', 'location_details'])->findOrFail($id);
            $this->view = 'super-admin.companies.ajax.headers';
            break;
        default:
            $this->company = Company::with('currency', 'package', 'approvalBy')
                ->withCount(['employees', 'fileStorage', 'clients', 'invoices', 'estimates', 'contracts', 'projects', 'tasks', 'leads', 'tickets', 'orders'])
                ->withSum('fileStorage', 'size')
                ->with(['companyAddress' => function ($query) {
                    return $query->where('is_default', 1);
                }])
                ->findOrFail($id);
            $this->company->user = Company::firstActiveAdmin($this->company);

            $this->pageTitle = $this->company->company_name;

            $this->latestInvoice = GlobalInvoice::where('company_id', $this->company->id)
                ->whereNotNull('pay_date')
                ->latest()
                ->first();

            $this->currency = $this->latestInvoice ? GlobalCurrency::where('id', $this->latestInvoice->currency_id)->withTrashed()->first() : global_setting()->currency;
            $this->view = 'super-admin.companies.ajax.show';
            break;
        }

        if (request()->ajax()) {
            $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'company';

        return view('super-admin.companies.show', $this->data);
    }

    public function editPackage($id)
    {
        $this->pageTitle = __('app.update') . ' ' . __('superadmin.package');

        $this->company = Company::with('package')->findOrFail($id);
        $this->packageSetting = PackageSetting::first();
        $this->packages = Package::all();
        $this->currentPackage = $this->company->package;

        $this->allPackages = collect();

        $packageInfo = [];

        foreach ($this->packages as $package) {
            $packageInfo[$package->id] = [
                'monthly' => $package->monthly_price,
                'annual' => $package->annual_price,
                'lifetime' => $package->price
            ];

            if ($package->default !== 'no' && $package->default !== 'lifetime') {
                $this->allPackages->push($this->getSeparatePackage($package));
            }
            else {

                if ($package->monthly_status) {
                    $this->allPackages->push($this->getSeparatePackage($package, 'monthly'));
                }

                if ($package->annual_status) {
                    $this->allPackages->push($this->getSeparatePackage($package));
                }
                if($package->package == 'lifetime') {
                    $this->allPackages->push($this->getSeparatePackage($package, 'lifetime'));
                }
            }
        }

        $this->packageInfo = $packageInfo;
        $this->pageInfo = request()->requestFrom ? request()->requestFrom : 'index';
        $this->latestInvoice = GlobalInvoice::where('company_id', $this->company->id)
            ->whereNotNull('pay_date')
            ->latest()->first();

        $this->currency = $this->latestInvoice ? GlobalCurrency::where('id', $this->latestInvoice->currency_id)->withTrashed()->first() : global_setting()->currency;

        $this->view = 'super-admin.companies.ajax.edit-package';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('super-admin.companies.create', $this->data);
    }

    private function getSeparatePackage(Package $package, $type = 'annual')
    {
        $newPackage = $package->replicate();
        $newPackage->id = $package->id;
        $newPackage->type = $type;
        $days = ($type == 'monthly') ? now()->addMonth()->diffInDays(now()) : now()->addYear()->diffInDays(now());

        if ($package->default == 'trial') {
            $days = PackageSetting::first()->no_of_days;
        }
        if ($package->default == 'lifetime') {
            $days = null;
        }

        $newPackage->days = $days;

        return $newPackage;
    }

    public function updatePackage(PackageUpdateRequest $request, $id)
    {
        $company = Company::with('package')->findOrFail($id);
        $package = Package::findOrFail($request->package);
        try {

            $company->package_id = $package->id;
            $company->package_type = $request->package_type;
            $company->status = 'active';

            $payDate = $request->pay_date ? Carbon::createFromFormat($this->global->date_format, $request->pay_date) : now();

            if ($request->trial_expire_on) {
                $company->licence_expire_on = Carbon::createFromFormat($this->global->date_format, $request->trial_expire_on)->format('Y-m-d');
            } elseif($package->package == 'lifetime') {
                $company->licence_expire_on = null;
            }
            else {
                $company->licence_expire_on = $request->licence_expire_on ? Carbon::createFromFormat($this->global->date_format, $request->licence_expire_on)->format('Y-m-d') : $payDate->copy()->addDays('7')->format('Y-m-d');
            }

            $nextPayDate = $request->next_pay_date ? Carbon::createFromFormat($this->global->date_format, $request->next_pay_date) : $company->licence_expire_on;
            $currencyId = $package->currency_id ?: global_setting()->currency_id;

            GlobalSubscription::where('company_id', $company->id)
                ->where('subscription_status', 'active')
                ->update(['subscription_status' => 'inactive']);

            $subscription = new GlobalSubscription();
            $subscription->company_id = $company->id;
            $subscription->package_id = $package->id;
            $subscription->currency_id = $currencyId;
            $subscription->package_type = $request->package_type;
            $subscription->quantity = 1;
            $subscription->gateway_name = 'offline';
            $subscription->subscription_status = 'active';
            $subscription->subscribed_on_date = now();
            $subscription->ends_at = $company->licence_expire_on;
            $subscription->transaction_id = str(str()->random(15))->upper();
            $subscription->save();

            $offlineInvoice = new GlobalInvoice();
            $offlineInvoice->global_subscription_id = $subscription->id;
            $offlineInvoice->company_id = $company->id;
            $offlineInvoice->currency_id = $currencyId;
            $offlineInvoice->package_id = $company->package_id;
            $offlineInvoice->package_type = $request->package_type;
            $offlineInvoice->total = ($request->amount ?: $package->{$request->package_type . '_price'}) ?: 0.00;
            $offlineInvoice->gateway_name = 'offline';
            $offlineInvoice->transaction_id = $subscription->transaction_id;

            if ($package->default != 'trial' && $package->default != 'lifetime') {
                $offlineInvoice->pay_date = $payDate;
                $offlineInvoice->next_pay_date = $nextPayDate;
            }

            $offlineInvoice->save();
            $company->save();

            if ($request->request_from == 'index') {
                return Reply::redirect(route('superadmin.companies.index'), __('messages.packageChanged'));
            }
            else {
                return Reply::redirect(route('superadmin.companies.show', [$company->id]), __('messages.packageChanged'));
            }

        } catch (\Throwable $th) {
            return Reply::error($th->getMessage());
        }

    }

    private function addEmployeeDetails($user, $employeeRole, $companyId)
    {
        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->company_id = $companyId;
        $employee->employee_id = 'EMP-' . $user->id;
        $employee->save();

        $search = new UniversalSearch();
        $search->searchable_id = $user->id;
        $search->company_id = $companyId;
        $search->title = $user->name;
        $search->route_name = 'employees.show';
        $search->save();

        // Assign Role
        $user->roles()->attach($employeeRole->id);
        /* @phpstan-ignore-line */
    }

    public function addUser($company, $request)
    {
        // Save Admin
        $user = User::withoutGlobalScopes([CompanyScope::class, ActiveScope::class])->where('company_id', $company->id)->where('email', $request->email)->first();

        if (is_null($user)) {
            $user = new User();
        }

        $userAuth = UserAuth::createUserAuthCredentials($request->email);

        $user->company_id = $company->id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = 'active';
        $user->user_auth_id = $userAuth->id;
        $user->locale = $company->locale;
        $user->country_id = user()->country_id;
        $user->save();

        if ($request->password != '') {
            UserAuth::where('id', $user->user_auth_id)->update(['password' => bcrypt($request->password)]);
        }

        if (!$user->hasRole('admin')) {

            // Attach Admin Role
            $adminRole = Role::withoutGlobalScope(CompanyScope::class)->where('name', 'admin')->where('company_id', $company->id)->first();

            $employeeRole = Role::withoutGlobalScope(CompanyScope::class)->where('name', 'employee')->where('company_id', $user->company_id)->first();

            $user->roles()->attach($adminRole->id);
            $this->addEmployeeDetails($user, $employeeRole, $company->id);


            $allPermissions = Permission::orderBy('id')->get()->pluck('id')->toArray();
            $permissionType = PermissionType::where('name', 'all')->first();

            foreach ($allPermissions as $permission) {
                $user->permissionTypes()->attach([
                    $permission => [
                        'permission_type_id' => $permissionType->id ?? PermissionType::ALL
                    ]]);
            }
        }
    }

    public function loginAsCompany($companyId)
    {
        $company = Company::findOrFail($companyId);
        $admin = Company::firstActiveAdmin($company);

        if (!$admin) {
            return Reply::error('Impersonating this company is not possible as there is no administrator.');
        }

        $user = user();
        session()->flush();
        session()->forget('user');

        Auth::logout();
        session(['impersonate' => $user->user_auth_id]);
        session(['impersonate_company_id' => $company->id]);
        session(['user' => $admin]);

        Auth::loginUsingId($admin->user_auth_id);

        return Reply::success(__('superadmin.successfullyLoginAsCompany'));
    }

    public function billing()
    {
        $this->managePermission = user()->permission('manage_billing');
        abort_403(!($this->managePermission == 'all'));

        $dataTable = new InvoiceDataTable();
        $tab = request('tab');
        $this->activeTab = $tab ?: 'company';

        $this->view = 'super-admin.companies.ajax.billing';
        // dd($this->company);
        return $dataTable->render('super-admin.companies.show', $this->data);
    }

    public function ajaxLoadCompany(Request $request)
    {
        $search = $request->search;

        $companies = [];

        if ($search) {
            $companies = Company::orderby('company_name')
                ->select('id', 'company_name', 'logo', 'light_logo')
                ->where('company_name', 'like', '%' . $search . '%')
                ->take(20)
                ->get();
        }

        $response = array();

        foreach ($companies as $company) {

            $response[] = array(
                'id' => $company->id,
                'text' => $company->company_name,
                'logo_url' => $company->logo_url,
            );

        }

        return response()->json($response);
    }

    public function approveCompany()
    {
        $companyId = request()->companyId;

        $company = Company::findOrFail($companyId);
        $company->approved = 1;
        $company->save();

        User::where('company_id', $company->id)->update(['admin_approval' => 1]);

        $user = Company::firstActiveAdmin($company);
        $user->notify(new CompanyApproved($company));

        return Reply::success(__('superadmin.companyApprovedSuccess'));

    }

}
