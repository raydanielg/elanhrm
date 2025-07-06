<?php

namespace App\Console\Commands\SuperAdmin;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use App\Notifications\SuperAdmin\LicenseExpire;
use App\Notifications\SuperAdmin\LicenseExpirePre;
use Illuminate\Console\Command;

class LicenceExpire extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licence-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set licence expire status of companies in companies table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $companies = Company::with('package')
            ->where('status', 'active')
            ->whereNotNull('licence_expire_on')
            ->where('licence_expire_on', '<', now()->format('Y-m-d'))
            ->orWhere('package_type', '=', 'lifetime')
            ->whereHas('package', function ($query) {
                $query->where('default', '!=', 'yes')->where('is_free', 0);
            })->get();

        $packages = Package::all();

        $defaultPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'yes';
        })->first();

        $otherPackages = $packages->filter(function ($value, $key) {
            return $value->default == 'no';
        });
        if($defaultPackage == null){
            return;
        }
        // Set default package for license expired companies.
        foreach ($companies as $company) {
            if($company->package != 'lifetime'){

                $latestInvoice = GlobalInvoice::where('company_id', $company->id)
                    ->whereNotNull('pay_date')
                    ->latest()->first();
                if (!($latestInvoice && $latestInvoice->next_pay_date->format('Y-m-d') > now()->format('Y-m-d'))) {
                    $company->package_id = $defaultPackage->id;
                    $company->licence_expire_on = now()->addYear();
                    $company->status = 'license_expired';
                    $company->save();

                    $this->updateSubscription($company, $defaultPackage);

                    $companyUser = Company::firstActiveAdmin($company);
                    $companyUser->notify(new LicenseExpire($company));
                }
            }


        }

        // Sent notification to companies before license expire.
        foreach ($otherPackages as $package) {
            if (!is_null($package->notification_before)) {
                $companiesNotify = Company::with('package')
                    ->where('status', 'active')
                    ->whereNotNull('licence_expire_on')
                    ->where('licence_expire_on', '<', now()->addDays($package->notification_before)->format('Y-m-d'))
                    ->whereHas('package', function ($query) use ($package) {
                        $query->where('default', '!=', 'yes')->where('is_free', 0)->where('id', $package->id);
                    })->get();

                foreach ($companiesNotify as $cmp) {
                    $companyUser = Company::firstActiveAdmin($cmp);
                    $companyUser->notify(new LicenseExpirePre($cmp));
                }
            }
        }
    }

    public function updateSubscription(Company $company, Package $package)
    {
        $packageType = $package->annual_status ? 'annual' : 'monthly';
        $currencyId = $package->currency_id ?: global_setting()->currency_id;

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
        $subscription->ends_at = $company->licence_expire_on;
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
        $offlineInvoice->next_pay_date = $company->licence_expire_on;
        $offlineInvoice->gateway_name = 'offline';
        $offlineInvoice->transaction_id = $subscription->transaction_id;
        $offlineInvoice->save();
    }

}
