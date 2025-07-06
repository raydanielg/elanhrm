<?php

namespace App\Http\Controllers\SuperAdmin;

use stdClass;
use Carbon\Carbon;
use App\Models\User;
use App\Helper\Reply;
use Razorpay\Api\Api;
use GuzzleHttp\Client;
use PayPal\Api\Agreement;
use Illuminate\Support\Str;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use PayPal\Auth\OAuthTokenCredential;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Config;
use App\Models\SuperAdmin\Subscription;
use Illuminate\Support\Facades\Session;
use App\Models\SuperAdmin\GlobalInvoice;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\AgreementStateDescriptor;
use App\Traits\SuperAdmin\MollieSettings;
use Illuminate\Support\Facades\Notification;
use App\Models\SuperAdmin\GlobalSubscription;
use net\authorize\api\contract\v1 as AnetAPI;
use App\Models\SuperAdmin\PayfastSubscription;
use App\Http\Controllers\AccountBaseController;
use App\Models\GlobalSetting;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\controller as AnetController;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\Package;
use App\Traits\PaymentGatewayTrait;
use Billow\Payfast;

class PayFastController extends AccountBaseController
{
    use MollieSettings, PaymentGatewayTrait;

    public function payFastPayment($package, $type, $company)
    {
        $plan = $package;
        $hash = global_setting()->hash;
        $globalInvoice = GlobalInvoice::with('package', 'company', 'currency', 'subscription', 'subscription')
            ->whereNotNull('pay_date')
            ->where('company_id', company()->id)->get();

        $firstInvoice = $globalInvoice->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->paid_on)->getTimestamp();
        })->first();

        $subcriptionCancel = true;

        if ($subcriptionCancel) {
            if ($plan->max_employees < $company->employees->count()) {
                return back()->withError('You can\'t downgrade package because your employees length is ' . $company->employees->count() . ' and package max employees count is ' . $plan->max_employees)->withInput();
            }

            $credential = new stdClass();
            $globalCredential = GlobalPaymentGatewayCredentials::first();

            if ($globalCredential->payfast_status != 'active') {
                return '';
            }

            if($globalCredential->payfast_mode == 'sandbox'){
                $passphrase = $credential->payfast_salt_passphrase = $globalCredential->test_payfast_passphrase;
                $credential->payfast_key = $globalCredential->test_payfast_merchant_id;
                $credential->payfast_secret = $globalCredential->test_payfast_merchant_key;
                $environment = 'https://sandbox.payfast.co.za/eng/process';
            }
            else{
                $passphrase = $credential->payfast_salt_passphrase = $globalCredential->payfast_passphrase;
                $credential->payfast_key = $globalCredential->payfast_merchant_id;
                $credential->payfast_secret = $globalCredential->payfast_merchant_key;
                $environment = 'https://www.payfast.co.za/eng/process';
            }

            $randomString = Str::random(30);
            $amount = $type == 'monthly' ? $package->monthly_price : $package->annual_price;
            $plan = $type == 'monthly' ? '3' : '6';
            $packageId = $package->id;
            $planType = strtolower($package->name).'_'.$type;
            $companyId = $company->id;
            // Construct variables
            $cartTotal = $amount;// This amount needs to be sourced from your application

            $subscription = GlobalSubscription::where('company_id', company()->id)->where('gateway_name', 'payfast')->where('subscription_status', 'inactive')->whereNull('ends_at')->latest()->first();

            $subscription = $subscription ? $subscription : new GlobalSubscription();
            $subscription->company_id = company()->id;
            $subscription->package_id = $package->id;
            $subscription->currency_id = $package->currency_id;
            $subscription->package_type = $type;
            $subscription->payfast_plan = $planType;

            if ($package->package_type == 'lifetime') {
                $subscription->quantity = 1;
                $subscription->payfast_status = 'active';
                $subscription->gateway_name = 'payfast';
                $subscription->subscription_status = 'inactive';
                $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
                $subscription->save();

                $subscriptionId = $subscription->id;

                $data = array(
                    'merchant_id' => $credential->payfast_key,
                    'merchant_key' => $credential->payfast_secret,
                    'return_url' => route('billing.payfast-success', compact('subscriptionId', 'cartTotal')),
                    'cancel_url' => route('billing.payfast-cancel'),
                    'notify_url' => route('payfast-notification', [$hash], compact('passphrase', 'packageId', 'planType', 'amount', 'type', 'companyId')),
                    'name_first' => user()->name,
                    'email_address' => user()->email,
                    // Transaction details
                    'm_payment_id' => $randomString, // Unique payment ID to pass through to notify_url
                    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
                    'item_name' => $package->name.' '.ucfirst($type),
                    'custom_int1' => company()->id,
                    'custom_int2' => $package->id,
                    'custom_int3' => $subscriptionId,
                    'custom_str1' => $type,
                    'custom_str2' => $planType,
                    // Subscription
                    'subscription_type' => '1',
                    'billing_date' => now()->format('Y-m-d'),
                    'recurring_amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
                    'frequency' => $plan,
                    'cycles' => '0'
                );

                $signature = $this->generateSignature($data, $credential->payfast_salt_passphrase);

                $data['signature'] = $signature;

                $htmlForm = '<form action="'.$environment.'" method="post" class="d-inline">';

                foreach($data as $name => $value)
                {
                    $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
                }

                $htmlForm .= '<button class="btn-light border rounded f-15 btn px-4 py-3 payFastPayment" type="submit">
                        <img style="height: 15px;" src="'.asset('img/payfast.png').'">
                            '.__('app.payfast').'
                        </button>';

                $htmlForm .= '</form>';

                return $htmlForm;
                }
            }

            $subscription->quantity = 1;
            $subscription->payfast_status = 'active';
            $subscription->gateway_name = 'payfast';
            $subscription->subscription_status = 'inactive';
            $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
            $subscription->save();

            $subscriptionId = $subscription->id;

            $data = array(
                // Merchant details
                'merchant_id' => $credential->payfast_key,
                'merchant_key' => $credential->payfast_secret,
                'return_url' => route('billing.payfast-success', compact('subscriptionId', 'cartTotal')),
                'cancel_url' => route('billing.payfast-cancel'),
                'notify_url' => route('payfast-notification', [$hash], compact('passphrase', 'packageId', 'planType', 'amount', 'type', 'companyId')),
                // Buyer details
                'name_first' => user()->name,
                'email_address' => user()->email,
                // Transaction details
                'm_payment_id' => $randomString, // Unique payment ID to pass through to notify_url
                'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
                'item_name' => $package->name.' '.ucfirst($type),
                'custom_int1' => company()->id,
                'custom_int2' => $package->id,
                'custom_int3' => $subscriptionId,
                'custom_str1' => $type,
                'custom_str2' => $planType,
                // Subscription
                'subscription_type' => '1',
                'billing_date' => now()->format('Y-m-d'),
                'recurring_amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
                'frequency' => $plan,
                'cycles' => '0'
            );

            $signature = $this->generateSignature($data, $credential->payfast_salt_passphrase);

            $data['signature'] = $signature;

            $htmlForm = '<form action="'.$environment.'" method="post" class="d-inline">';

            foreach($data as $name => $value)
            {
                $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
            }

            $htmlForm .= '<button class="btn-light border rounded f-15 btn px-4 py-3 payFastPayment" type="submit">
                            <img style="height: 15px;" src="'.asset('img/payfast.png').'">
                                '.__('app.payfast').'
                            </button>';

            $htmlForm .= '</form>';

            return $htmlForm;
        }


    public function generateSignature($data, $passPhrase = null)
    {
        // Create parameter string
        $pfOutput = '';

        foreach( $data as $key => $val ) {

            if($val !== '') {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }

        }

        // Remove last ampersand
        $getString = substr( $pfOutput, 0, -1 );

        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }

        return md5( $getString );
    }

    public function payFastPaymentSuccess(Request $request)
    {
        try {
            $subscription = GlobalSubscription::find($request->subscriptionId);

            if($subscription){
                $subscription->subscription_status = 'active';
                $subscription->transaction_id = $request->token;
                $subscription->save();

                $invoice = GlobalInvoice::where('global_subscription_id', $subscription->id)->first();
                $invoice = $invoice ? $invoice : new GlobalInvoice();
                $invoice->company_id = $subscription->company_id;
                $invoice->package_id = $subscription->package_id;
                $invoice->currency_id = $subscription->currency_id;
                $invoice->global_subscription_id = $subscription->id;
                $invoice->pay_date = now()->format('Y-m-d');
                $invoice->next_pay_date = now()->{(($subscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d');
                $invoice->status = 'active';
                $invoice->package_type = $subscription->package_type;
                $invoice->gateway_name = 'payfast';
                $invoice->total = $request->cartTotal;
                $invoice->save();

                $company = company();
                $company->package_id = $subscription->package_id;
                $company->package_type = $subscription->package_type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;
                $company->save();

                // Send superadmin notification
                $generatedBy = User::allSuperAdmin();
                $allAdmins = User::allAdmins($company->id);
                Notification::send($generatedBy, new CompanyUpdatedPlan($company, $subscription->package_id));
                Notification::send($allAdmins, new CompanyUpdatedPlan($company, $subscription->package_id));
                Session::put('success', __('superadmin.paymentSuccessfullyDone', ['package' => company()->package->name, 'planType' => company()->package_type]));
            }

            return Redirect::route('billing.index');

        } catch (\Exception $e) {
            error_log($e->getMessage());
            \session()->put('error', $e->getMessage());
            return redirect()->route('billing.upgrade_plan');
        }
    }

    public function payfastCancelSubscription($type=null)
    {
        $credential = GlobalPaymentGatewayCredentials::first();

        if ($type == 'paypal') {
            $paypal_conf = Config::get('paypal');
            $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
            $api_context->setConfig($paypal_conf['settings']);

            $paypalInvoice = GlobalInvoice::where('gateway_name', 'paypal')->whereNotNull('transaction_id')->whereNull('end_on')
                ->where('company_id', company()->id)->where('status', 'paid')->first();

            if ($paypalInvoice) {
                $agreementId = $paypalInvoice->transaction_id;
                $agreement = new Agreement();
                $paypalInvoice = GlobalInvoice::where('gateway_name', 'paypal')->whereNotNull('transaction_id')->whereNull('end_on')
                    ->where('company_id', company()->id)->where('status', 'paid')->first();

                $agreement->setId($agreementId);
                $agreementStateDescriptor = new AgreementStateDescriptor();
                $agreementStateDescriptor->setNote('Cancel the agreement');

                try {
                    $agreement->cancel($agreementStateDescriptor, $api_context);
                    $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                    // Set subscription end date
                    $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                    $paypalInvoice->save();
                } catch (\Exception $ex) {
                    \Session::put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
            }

        } elseif ($type == 'razorpay') {

            $apiKey    = $credential->razorpay_key;
            $secretKey = $credential->razorpay_secret;
            $api       = new Api($apiKey, $secretKey);

            // Get subscription for unsubscribe
            $subscriptionData = GlobalSubscription::where('gateway_name', 'razorpay')->where('company_id', company()->id)->whereNull('ends_at')->first();

            if ($subscriptionData) {
                try {
                    $subscription  = $api->subscription->fetch($subscriptionData->subscription_id);

                    if ($subscription->status == 'active') {

                        // unsubscribe plan
                        $subData = $api->subscription->fetch($subscriptionData->subscription_id)->cancel(['cancel_at_cycle_end' => 1]);

                        // plan will be end on this date
                        $subscriptionData->ends_at = \Carbon\Carbon::createFromTimestamp($subData->end_at)->format('Y-m-d');
                        $subscriptionData->save();
                    }
                } catch (\Exception $ex) {
                    \Session::put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
                return Reply::redirectWithError(route('billing.packages'), 'There is no data found for this subscription');
            }

        } elseif ($type == 'paystack') {
            // Get subscription for unsubscribe
            $this->setPaystackConfigs();
            $subscriptionData = GlobalSubscription::where('gateway_name', 'paystack')->where('company_id', company()->id)->where('status', 'active')->first();

            if ($subscriptionData) {
                try {
                    $paystack = new Paystack();
                    $paystack->code = $subscriptionData->subscription_id;
                    $paystack->token = $subscriptionData->token;

                    $paystack->disableSubscription();

                    $subscriptionData->status = 'inactive';
                    $subscriptionData->save();

                } catch (\Exception $ex) {
                    \Session::put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
            }

        } elseif ($type == 'mollie') {
            // Get subscription for unsubscribe
            $this->setMollieConfigs();
            $subscriptionData = GlobalSubscription::where('gateway_name', 'mollie')->where('company_id', company()->id)->where('subscription_status', 'active')->whereNull('ends_at')->latest()->first();

            if ($subscriptionData) {
                try {
                    Mollie::api()->subscriptions()->cancelForId($subscriptionData->customer_id, $subscriptionData->transaction_id);
                    $subscriptionData->ends_at = now();
                    $subscriptionData->subscription_status = 'inactive';
                    $subscriptionData->save();
                } catch (\Exception $ex) {

                    session()->put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
            }

        }  elseif ($type == 'authorize') {
            // Get subscription for unsubscribe
            $this->setMollieConfigs();
            $subscriptionData = GlobalSubscription::where('gateway_name', 'authorize')->where('company_id', company()->id)->first();

            if ($subscriptionData) {
                try {

                    $credential = GlobalPaymentGatewayCredentials::first();
                    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

                    $merchantAuthentication->setName($credential->authorize_api_login_id);
                    $merchantAuthentication->setTransactionKey($credential->authorize_transaction_key);

                    // Set the transaction's refId
                    $refId = 'ref' . time();

                    $request = new AnetAPI\ARBCancelSubscriptionRequest();
                    $request->setMerchantAuthentication($merchantAuthentication);
                    $request->setRefId($refId);
                    $request->setSubscriptionId($subscriptionData->subscription_id);

                    $controller = new AnetController\ARBCancelSubscriptionController($request);

                    if($credential->authorize_environment == 'sandbox') {
                        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                    }
                    else {
                        $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                    }

                    if (($response != null) && ($response->getMessages()->getResultCode() == 'Ok'))
                    {
                        $subscriptionData->ends_at = now();
                        $subscriptionData->save();
                    }
                    else
                    {
                        $errorMessages = $response->getMessages()->getMessage();
                        return Reply::error($errorMessages[0]->getText());
                    }

                } catch (\Exception $ex) {

                    \Session::put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
            }

        } elseif ($type == 'payfast') {
            $credential = new stdClass();
            $globalCredential = GlobalPaymentGatewayCredentials::first();

            if($globalCredential->payfast_mode == 'sandbox'){
                $credential->payfast_salt_passphrase = $globalCredential->test_payfast_passphrase;
                $credential->payfast_key = $globalCredential->test_payfast_merchant_id;
                $credential->payfast_secret = $globalCredential->test_payfast_merchant_key;
                $cancelSandbox = '?testing=true';

            }
            else{
                $credential->payfast_salt_passphrase = $globalCredential->payfast_passphrase;
                $credential->payfast_key = $globalCredential->payfast_merchant_id;
                $credential->payfast_secret = $globalCredential->payfast_merchant_key;
                $cancelSandbox = '';
            }

            $payfastInvoice = GlobalInvoice::where('gateway_name', 'payfast')->latest()->first();
            $date = now()->format('Y-m-d\TH:i:s');
            try{
                $url = 'https://api.payfast.co.za/subscriptions/'.$payfastInvoice->token.'/cancel'.$cancelSandbox;
                $header = ['merchant-id' => $credential->payfast_key, 'version' => 'v1' , 'timestamp' => $date, 'signature' => $payfastInvoice->signature];
                $client = new Client();
                $res = $client->request('PUT', $url, ['headers' => $header]);

                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);

                if($conversionRate['status'] == 'success'){
                    $paydate = $payfastInvoice->pay_date;

                    if(company()->package_type == 'monthly'){
                        $newDate = Carbon::createFromDate($paydate)->addMonth()->format('Y-m-d');
                    }
                    else {
                        $newDate = Carbon::createFromDate($paydate)->addYear()->format('Y-m-d');
                    }

                    $subscription = PayfastSubscription::orderBy('id', 'DESC')->first();
                    $subscription->ends_at = $newDate;
                    $subscription->save();

                }

            } catch(\Exception $ex) {
                \Session::put('error', $ex->getMessage());
                return redirect()->route('billing.upgrade_plan');
            }

        } else {
            $company = company();
            $subscription = Subscription::where('company_id', company()->id)->whereNull('ends_at')->first();

            if ($subscription) {
                try {
                    $company->subscription('primary')->cancel();
                    $company->subscription('primary')->cancel();
                } catch (\Exception $ex) {
                    \Session::put('error', $ex->getMessage());
                    return redirect()->route('billing.upgrade_plan');
                }
            }

        }

        return Reply::redirect(route('billing.index'), __('messages.unsubscribeSuccess'));
    }

    public function payFastPaymentCancel()
    {
        \Session::put('error', __('messages.paymentFailed'));
        return Redirect::route('billing.index');
    }

    public function handleGatewayCallback($id, $type, $status)
    {
        $plan = Package::findOrFail($id);
        $company = company();

        $this->makePayment('Payfast', $plan->monthly_price, $company, 'payfast_' . $plan->id, ($status == 'success' ? 'complete' : 'failed'));
        return redirect(url()->temporarySignedRoute('billing.index', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), $company->hash));

    }

     public function handleGatewayWebhook(Request $request, $companyHash)
    {
        $this->payfastSet($companyHash);

            $company = company();
            $plan = Package::findOrFail($request->custom_int1);
            // $invoice->status = ($request->payment_status == 'COMPLETE') ? 'paid' : 'unpaid';
            $this->makePayment('Payfast', $request->amount_gross, $company, $request->m_payment_id, (($request->payment_status == 'COMPLETE') ? 'complete' : 'failed'));

                $invoice = new GlobalInvoice();
                $invoice->company_id = $company->id;
                $invoice->package_id = $plan->id;
                $invoice->currency_id = $plan->currency_id;
                $invoice->pay_date = now()->format('Y-m-d');
                $invoice->status = 'active';
                $invoice->package_type = $plan->package_type;
                $invoice->gateway_name = 'payfast';
                $invoice->total = $request->cartTotal;
                $invoice->save();

                $company = company();
                $company->package_id = $plan->package_id;
                $company->package_type = $plan->package_type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;
                $company->save();

                // Send superadmin notification
                $generatedBy = User::allSuperAdmin();
                $allAdmins = User::allAdmins($company->id);
                Notification::send($generatedBy, new CompanyUpdatedPlan($company, $plan->package_id));
                Notification::send($allAdmins, new CompanyUpdatedPlan($company, $plan->package_id));

                return response()->json(['status' => 'success']);
    }

}
