<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use App\Models\SuperAdmin\Package;
use App\Traits\SuperAdmin\PaystackSettings;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;
use Unicodeveloper\Paystack\Paystack;

class PaystackController extends Controller
{

    use PaystackSettings;

    protected $client;

    /**
     * Redirect the User to Paystack Payment Page
     */
    public function redirectToGateway(Request $request)
    {

        $this->setPaystackConfigs();
        $package = Package::find($request->plan_id);
        $paystack = new Paystack();
        $request->first_name = $request->name;
        $request->orderID = '1';
        $request->amount = $package->{$request->type . '_price'};
        $request->quantity = '1';
        $request->callback_url = route('billing.paystack.callback');
        $request->reference = $paystack->genTranxRef();
        $request->key = config('paystack.secretKey');
        $request->plan = $package->{'paystack_' . $request->type . '_plan_id'};
        $request->currency = $package->currency->currency_code;

        $subscription = GlobalSubscription::where('gateway_name', 'paystack')->where('company_id', company()->id)->where('subscription_status', 'inactive')->whereNull('ends_at')->latest()->first();
        $subscription = $subscription ?: new GlobalSubscription();

        $subscription->company_id = company()->id;
        $subscription->package_id = $package->id;
        $subscription->currency_id = $package->currency_id;
        $subscription->package_type = ($package->package_type != 'lifetime') ? $request->type : 'lifetime';
        $subscription->gateway_name = 'paystack';
        $subscription->subscription_status = 'inactive';
        $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
        $subscription->subscribed_on_date = now()->format('Y-m-d');
        $subscription->save();

        $request->metadata = [
            'subscription_id' => $subscription->id,
            'package_amount' => $package->{$request->type . '_price'},
        ];

        // Customer details
        $request->email = $request->paystackEmail;
        $request->phone = company()->company_phone;
        $request->fname = company()->company_name;
        $request->additional_info = [
            'company_id' => company()->id,
            'subscription_id' => $subscription->id,
        ];

        $customer = $paystack->createCustomer();

        $subscription->customer_id = $customer['data']['customer_code'];
        $subscription->save();

        session([
            'subscription_id' => $subscription->id,
            'package_amount' => $package->{$request->type . '_price'},
        ]);

        return $paystack->getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $this->setPaystackConfigs();
        $paystack = new Paystack();
        $paymentDetails = $paystack->getPaymentData();

        if ($paymentDetails['status'] && ($paymentDetails['data']['status'] == 'success')) {

            $globalSubscription = GlobalSubscription::find($paymentDetails['data']['metadata']['subscription_id']);
            GlobalSubscription::where('company_id', $globalSubscription->company_id)->where('subscription_status', 'active')->update(['subscription_status' => 'inactive', 'ends_at' => now()]);

            $globalSubscription->subscription_status = 'active';
            $globalSubscription->subscribed_on_date = now();
            $globalSubscription->customer_id = $paymentDetails['data']['customer']['customer_code'];
            $globalSubscription->save();

            $invoice = GlobalInvoice::where('transaction_id', $paymentDetails['data']['reference'])->first();
            $package = Package::find($globalSubscription->package_id);
            $invoice = $invoice ?: new GlobalInvoice();

            $invoice->company_id = $globalSubscription->company_id;
            $invoice->package_id = $globalSubscription->package_id;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->pay_date = now()->format('Y-m-d');
            $invoice->next_pay_date = ($package->package_type != 'lifetime') ? now()->{(($globalSubscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d') : null;
            $invoice->status = 'active';
            $invoice->package_type = ($package->package_type != 'lifetime') ? $globalSubscription->package_type : 'lifetime';
            $invoice->gateway_name = 'paystack';
            $invoice->total = $paymentDetails['data']['metadata']['package_amount'];
            $invoice->transaction_id = $paymentDetails['data']['reference'];
            $invoice->token = $paymentDetails['data']['authorization']['authorization_code'];
            $invoice->signature = $paymentDetails['data']['authorization']['signature'];
            $invoice->save();

            $company = company();
            $company->package_id = $globalSubscription->package_id;
            $company->package_type = $globalSubscription->package_type;

            // Set company status active
            $company->status = 'active';
            $company->licence_expire_on = null;
            $company->save();

            // Send superadmin notification
            $generatedBy = User::allSuperAdmin();
            $allAdmins = User::allAdmins($company->id);
            Notification::send($generatedBy, new CompanyUpdatedPlan($company, $globalSubscription->package_id));
            Notification::send($allAdmins, new CompanyUpdatedPlan($company, $globalSubscription->package_id));
            session()->put('success', __('superadmin.paymentSuccessfullyDone', ['package' => company()->package->name, 'planType' => company()->package_type]));
        }
        else {
            session()->put('error', __('superadmin.paymentFailed'));
        }

        return redirect()->route('billing.index');
    }

}
