<?php

namespace App\Http\Controllers\SuperAdmin;

use stdClass;
use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;

class PayFastWebhookController extends Controller
{

    public function saveInvoice(Request $request)
    {
        $credential = new stdClass();

        $globalCredential = GlobalPaymentGatewayCredentials::first();

        if ($globalCredential->payfast_mode == 'sandbox') {
            $credential->payfast_salt_passphrase = $globalCredential->test_payfast_passphrase;
            $credential->payfast_key = $globalCredential->test_payfast_merchant_id;
            $credential->payfast_secret = $globalCredential->test_payfast_merchant_key;
            $pfHost = 'sandbox.payfast.co.za';
        }
        else {
            $credential->payfast_salt_passphrase = $globalCredential->payfast_passphrase;
            $credential->payfast_key = $globalCredential->payfast_merchant_id;
            $credential->payfast_secret = $globalCredential->payfast_merchant_key;
            $pfHost = 'www.payfast.co.za';
        }

        $pfParamString = '';
        // Tell PayFast that this page is reachable by triggering a header 200
        header('HTTP/1.0 200 OK');
        flush();


        // Posted variables from ITN
        // phpcs:ignore
        $pfData = $_POST;

        // Strip any slashes in data
        foreach ($pfData as $key => $val) {
            $pfData[$key] = stripslashes($val);
        }
        // Convert posted variables to a string
        foreach ($pfData as $key => $val) {

            if ($key !== 'signature') {
                $pfParamString .= $key . '=' . urlencode($val) . '&';
            }
            else {
                break;
            }

        }

        $pfParamString = substr($pfParamString, 0, -1);
        $passphrase = $credential->payfast_salt_passphrase;

        $paydate = $pfData['billing_date'];

        if ($request->custom_str1 == 'monthly') {
            $newDate = Carbon::createFromDate($paydate)->addMonth()->format('Y-m-d');
        }
        else {
            $newDate = Carbon::createFromDate($paydate)->addYear()->format('Y-m-d');
        }

        $check1 = $this->pfValidSignature($pfData, $pfParamString, $passphrase);
        $check2 = $this->pfValidIP();
        $check4 = $this->pfValidServerConfirmation($pfParamString, $pfHost);

        if ($check1 && $check2 && $check4) {
            $subscription = GlobalSubscription::where('gateway_name', 'payfast')->latest()->first();

            if (!$subscription) {
                return true;
            }

            GlobalSubscription::where('company_id', $subscription->company_id)->where('subscription_status', 'active')->update(['subscription_status' => 'inactive', 'ends_at' => now()]);
            $subscription->subscription_status = 'active';
            $subscription->transaction_id = $request->token;
            $subscription->save();
            $invoice = GlobalInvoice::where('global_subscription_id', $subscription->id)->whereNull('transaction_id')->orWhere('transaction_id', $request->token)->first();
            $invoice = new GlobalInvoice();
            $invoice->company_id = $request->custom_int1;
            $invoice->package_id = $request->custom_int2;
            $invoice->currency_id = $subscription->currency_id;
            $invoice->global_subscription_id = $subscription->id;
            $invoice->m_payment_id = $request->m_payment_id;
            $invoice->pf_payment_id = $request->pf_payment_id;
            $invoice->transaction_id = $request->token;
            $invoice->payfast_plan = $request->custom_str2;
            $invoice->total = $request->amount_gross;
            $invoice->pay_date = $request->billing_date;
            $invoice->next_pay_date = carbon::parse($newDate)->format('Y-m-d');
            $invoice->signature = $request->signature;
            $invoice->token = $request->token;
            $invoice->status = 'active';
            $invoice->package_type = $request->custom_str1;
            $invoice->gateway_name = 'payfast';
            $invoice->save();

            $company = Company::find($subscription->company_id);
            $company->package_id = $subscription->package_id;
            $company->package_type = $subscription->package_type;

            // Set company status active
            $company->status = 'active';
            $company->licence_expire_on = null;
            $company->save();
        }
    }

    public function pfValidSignature($pfData, $pfParamString, $passphrase)
    {
        // Calculate security signature
        if ($passphrase === null) {
            $tempParamString = $pfParamString;

        }
        else {
            $tempParamString = $pfParamString . '&passphrase=' . urlencode($passphrase);
        }

        $signature = md5($tempParamString);

        return ($pfData['signature'] === $signature);
    }

    // phpcs:ignore
    public function pfValidIP()
    {
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        );

        $validIps = [];

        foreach ($validHosts as $pfHostname) {

            $ips = gethostbynamel($pfHostname);

            if ($ips !== false) {
                $validIps = array_merge($validIps, $ips);
            }

        }

        // Remove duplicates
        $validIps = array_unique($validIps);
        $referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);

        if (in_array($referrerIp, $validIps, true)) {
            return true;
        }

        return false;
    }

    public function pfValidServerConfirmation($pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null)
    {
        // Use cURL (if available)

        if (in_array('curl', get_loaded_extensions(), true)) {
            // Variable initialization
            $url = 'https://' . $pfHost . '/eng/query/validate';

            // Create default cURL object
            $ch = curl_init();

            // Set cURL options - Use curl_setopt for greater PHP compatibility
            // Base settings
            curl_setopt($ch, CURLOPT_USERAGENT, null);  // Set user agent
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Return output as string rather than outputting it
            curl_setopt($ch, CURLOPT_HEADER, false);             // Don't include header in output
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            // Standard settings
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pfParamString);

            if (!empty($pfProxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $pfProxy);
            }

            // Execute cURL
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === 'VALID') {
                return true;
            }

        }

        return false;

    }

}
