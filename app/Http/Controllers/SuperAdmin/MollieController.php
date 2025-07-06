<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Package;
use Mollie\Laravel\Facades\Mollie;
use App\Http\Controllers\Controller;
use Mollie\Api\Exceptions\ApiException;
use App\Models\SuperAdmin\GlobalInvoice;
use Illuminate\Support\Facades\Redirect;
use App\Traits\SuperAdmin\MollieSettings;
use Illuminate\Support\Facades\Notification;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;

class MollieController extends Controller
{
    use MollieSettings;
    protected $client;

    /**
     * Redirect the User to Paystack Payment Page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGateway(Request $request)
    {
        $package = Package::find($request->plan_id);
        $this->setMollieConfigs();
        $hash = global_setting()->hash;
        $packageType = ($package->package_type != 'lifetime') ? $request->type : 'lifetime';

        $subscription = GlobalSubscription::where('gateway_name', 'mollie')->where('company_id', company()->id)->where('subscription_status', 'inactive')->whereNull('ends_at')->latest()->first();
        $subscription = $subscription ? $subscription : new GlobalSubscription();

        $customer = Mollie::api()->customers()->create([
            'name'  => $request->name,
            'email' => $request->mollieEmail,
        ]);

        $subscription->company_id = company()->id;
        $subscription->customer_id = $customer->id;
        $subscription->package_id = $package->id;
        $subscription->package_type = $packageType;
        $subscription->currency_id = $package->currency_id;
        $subscription->gateway_name = 'mollie';
        $subscription->subscription_status = 'inactive';
        $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
        $subscription->save();

        $metadata = [
            'company_id' => company()->id,
            'subscription_id' => $subscription->id,
            'package_id' => $package->id,
            'package_type' => $subscription->package_type,
            'package_amount' => $package->{$subscription->package_type . '_price'},
        ];
        try {
            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => $package->currency->currency_code,
                    'value'    => number_format((float)$package->{$request->type . '_price'}, 2, '.', ''), // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'customerId'   => $customer->id,
                'sequenceType' => 'first',
                'description'  => $package->name . ' payment',
                'redirectUrl'  => route('billing.mollie.callback', ['paymentId' => 1, $hash]),
                'metadata' => $metadata,
            ]);

            session(['paymentId' => $payment->id]);

            $payment->redirectUrl = route('billing.mollie.callback', ['paymentId' => $payment->id, $hash]);
            $payment->update();

            // redirect customer to Mollie checkout page
            return redirect($payment->getCheckoutUrl(), 303);

        } catch (ApiException $e) {
            if ($e->getField() == 'webhookUrl' && $e->getCode() == '422') {
                return redirect()->back()->with('error', __('superadmin.messages.mollieLocalhost'). $e->getMessage());
            }

            session()->put('error', $e->getMessage());
            return redirect()->route('billing.upgrade_plan');
        } catch (\Exception $e) {
            session()->put('error', $e->getMessage());
            return redirect()->route('billing.upgrade_plan');
        }

        return Redirect::route('billing.index');
    }

    /**
     * Obtain the User payment information from Mollie
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGatewayCallback($paymentId, $hash)
    {
        if($paymentId == 1){
            $paymentId = session('paymentId');
        }

        $this->setMollieConfigs();

        $payment = Mollie::api()->payments()->get($paymentId);

        if ($payment->isPaid()) {
            $globalSubscription = GlobalSubscription::find($payment->metadata->subscription_id);

            GlobalSubscription::where('company_id', $globalSubscription->company_id)->where('subscription_status', 'active')->update(['subscription_status' => 'inactive']);
            $package = Package::find($globalSubscription->package_id);

            $customer = Mollie::api()->customers()->get($globalSubscription->customer_id);
            $interval = ($globalSubscription->package_type == 'monthly') ? '1 month' : '12 month';
            $subscription = Mollie::api()->subscriptions()->createFor($customer, [
                'amount' => [
                    'currency' => $package->currency->currency_code,
                    'value'    => number_format((float)$package->{$globalSubscription->package_type . '_price'}, 2, '.', ''), // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'interval' => $interval,
                'description'  => company()->company_name . ' subscribed',
                'webhookUrl'  => route('billing.mollie.webhook', [$globalSubscription, $hash]),
                'startDate' => now()->{(($globalSubscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d'),
            ]);


            $globalSubscription->transaction_id = $subscription->id;
            $globalSubscription->subscription_status = 'active';
            $globalSubscription->subscribed_on_date = now();
            $globalSubscription->save();

            $invoice = GlobalInvoice::where('transaction_id', $payment->id)->first();
            $invoice = $invoice ? $invoice : new GlobalInvoice();
            $invoice->company_id = $globalSubscription->company_id;
            $invoice->package_id = $globalSubscription->package_id;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->pay_date = now()->format('Y-m-d');
            $invoice->next_pay_date = ($package->package_type != 'lifetime') ? now()->{(($globalSubscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d') : null;
            $invoice->status = 'active';
            $invoice->package_type = $globalSubscription->package_type;
            $invoice->gateway_name = 'mollie';
            $invoice->total = $payment->amount->value;
            $invoice->transaction_id = $payment->id;
            $invoice->token = $subscription->id;
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
            return redirect(route('billing.index'));
        }

        if(!$payment->isPaid()){
            session()->put('error', __('superadmin.paymentFailed'));
        }

        return redirect(route('billing.upgrade_plan'));
    }

    /**
     * handleGatewayWebhook
     *
     * @param  Request $request
     * @param  mixed $hash
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function handleGatewayWebhook(Request $request, $subscriptionId, $hash)
    {

        $this->setMollieConfigs();

        $payment = Mollie::api()->payments()->get($request->id);

        if ($payment->isPaid()) {
            $globalSubscription = GlobalSubscription::find($subscriptionId);
            $package = Package::find($globalSubscription->package_id);
            $oldInvoice = GlobalInvoice::where('global_subscription_id', $globalSubscription->id)->where('status', 'active')->whereNot('transaction_id', $payment->id)->latest()->first();

            $invoice = GlobalInvoice::where('transaction_id', $payment->id)->first();
            $invoice = $invoice ? $invoice : new GlobalInvoice();
            $invoice->company_id = $globalSubscription->company_id;
            $invoice->package_id = $globalSubscription->package_id;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->pay_date = now()->format('Y-m-d');
            $invoice->next_pay_date = ($package->package_type != 'lifetime') ? now()->{(($globalSubscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d') : null;
            $invoice->status = 'active';
            $invoice->package_type = $globalSubscription->package_type;
            $invoice->gateway_name = 'mollie';
            $invoice->total = $payment->amount->value;
            $invoice->transaction_id = $payment->id;
            $invoice->token = $oldInvoice?->token;
            $invoice->save();
        }

        return response('OK');
    }

}
