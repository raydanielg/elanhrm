<?php

namespace App\Console\Commands\SuperAdmin;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\PackageSetting;
use App\Notifications\SuperAdmin\TrialLicenseExp;
use App\Notifications\SuperAdmin\TrialLicenseExpPre;
use Illuminate\Console\Command;

class TrialExpire extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set trial expire status of companies in companies table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trialPackage = Package::where('default', 'trial')->first();
        $defaultPackage = Package::where('default', 'yes')->first();
        $setting = PackageSetting::first();

        if ($defaultPackage->annual_status) {
            $expireDate = now()->addYear()->format('Y-m-d');
        }
        else {
            $expireDate = now()->addMonth()->format('Y-m-d');
        }

        $this->notifyCompanyOnNotificationDays($setting, $trialPackage);

        // Before today is just incase cron job is missed for today
        $companiesNotify = Company::with('package')
            ->where('status', 'active')
            ->whereNotNull('licence_expire_on')
            ->whereDate('licence_expire_on', '<=', now()->format('Y-m-d'))
            ->whereHas('package', function ($query) use ($trialPackage) {
                $query->where('default', 'trial')->where('id', $trialPackage->id);
            })
            ->get();

        foreach ($companiesNotify as $cmp) {
            $companyUser = Company::firstActiveAdmin($cmp);

            $cmp->package_id = $defaultPackage->id;
            $cmp->licence_expire_on = $expireDate;
            $cmp->status = 'license_expired';
            $cmp->save();

            $this->updateSubscription($cmp, $defaultPackage);

            $companyUser->notify(new TrialLicenseExp($cmp));
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

    private function notifyCompanyOnNotificationDays($setting, $trialPackage)
    {
        if ($setting->notification_before) {
            $companiesOnTrial = Company::with('package')
                ->where('status', 'active')
                ->whereNotNull('licence_expire_on')
                ->where('licence_expire_on', now()->addDays($setting->notification_before)->format('Y-m-d'))
                ->whereHas('package', function ($query) use ($trialPackage) {
                    $query->where('default', 'trial')->where('id', $trialPackage->id);
                })->get();

            foreach ($companiesOnTrial as $cmp) {
                $companyUser = Company::firstActiveAdmin($cmp);
                $companyUser->notify(new TrialLicenseExpPre($cmp));
            }
        }
    }

}
