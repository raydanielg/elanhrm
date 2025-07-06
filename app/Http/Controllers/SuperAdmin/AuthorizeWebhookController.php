<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use App\Models\User;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controller;

class AuthorizeWebhookController extends Controller
{

    public function saveInvoices(Request $request, $id)
    {
        if($request->eventType == 'net.authorize.customer.subscription.created') {
            $subscription = GlobalSubscription::where('gateway_name', 'authorize')->where('transaction_id', $request->payload['id'])->first();

            $package = Package::find($subscription->package_id);

            $company = Company::findOrFail($subscription->company_id);

            $authorizeInvoices = GlobalInvoice::where('gateway_name', 'authorize')->where('transaction_id', $request->payload['profile']['customerPaymentProfileId'])->first();

            if (!$authorizeInvoices) {
            $authorizeInvoices = new GlobalInvoice();
            }

            $authorizeInvoices->company_id = $subscription->company_id;
            $authorizeInvoices->package_id = $subscription->package_id;
            $authorizeInvoices->transaction_id = $request->payload['profile']['customerPaymentProfileId'];
            $authorizeInvoices->total = $package->{$subscription->package_type.'_price'};
            $authorizeInvoices->pay_date = now()->format('Y-m-d');
            $authorizeInvoices->gateway_name = $subscription->gateway_name;
            $authorizeInvoices->currency_id = $package->currency_id;
            $authorizeInvoices->global_subscription_id = $subscription->id;
            $authorizeInvoices->status = 'active';
            $packageType = $subscription->package_type;

            if($packageType == 'monthly') {
            $authorizeInvoices->next_pay_date = now()->addMonth()->format('Y-m-d');
            } elseif($packageType == 'annual') {
            $authorizeInvoices->next_pay_date = now()->addYear()->format('Y-m-d');
            } else {
            $authorizeInvoices->next_pay_date = null; // For lifetime package
            }

            $authorizeInvoices->save();

            $company->package_id = $authorizeInvoices->package_id;
            $company->package_type = ($packageType == 'annual') ? 'annual' : (($packageType == 'monthly') ? 'monthly' : 'lifetime');
            $company->status = 'active';
            $company->licence_expire_on = ($packageType == 'lifetime') ? null : $company->licence_expire_on;
            $company->save();

            // Send superadmin notification
            $generatedBy = User::allSuperAdmin();
            $allAdmins = User::allAdmins($company->id);
            Notification::send($generatedBy, new CompanyUpdatedPlan($company, $company->package_id));
            Notification::send($allAdmins, new CompanyUpdatedPlan($company, $company->id));
        }

    }

}
