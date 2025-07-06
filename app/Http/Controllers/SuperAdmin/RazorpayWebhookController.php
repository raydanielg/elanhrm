<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Notifications\SuperAdmin\CompanyPurchasedPlan;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\RazorpayInvoice;
use App\Models\User;
use Carbon\Carbon;
use Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controller;
use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class RazorpayWebhookController extends Controller
{

    const SUBSCRIPTION_CHARGED = 'subscription.charged';
    const PAYMENT_FAILED = 'payment.failed';
    const SUBSCRIPTION_CANCELLED = 'subscription.cancelled';

    public function saveInvoices(Request $request)
    {
        if (!isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'])) {
            echo('Signature mismatched Razorpay webhook saas');

            return false;
        }

        $credential = GlobalPaymentGatewayCredentials::first();

        if ($credential->razorpay_mode == 'test') {
            $apiKey = $credential->test_razorpay_key;
            $secretKey = $credential->test_razorpay_secret;
            $secretWebhook = $credential->test_razorpay_webhook_secret;
        }
        else {
            $apiKey = $credential->live_razorpay_key;
            $secretKey = $credential->live_razorpay_secret;
            $secretWebhook = $credential->live_razorpay_webhook_secret;
        }

        $post = file_get_contents('php://input');
        $requestData = json_decode($post, true);

        $notes = $requestData['payload']['payment']['entity']['notes'] ?? null;

        if (is_null($notes)) {
            echo('Notes Payload not found');
            return false;
        }


        if (isset($notes['webhook_hash']) && $notes['webhook_hash'] !== global_setting()->hash) {
            echo('Main app hash mismatched: This indicates that the webhook for another app has been called.');

            return false;
        }

        $razorpayWebhookSecret = $secretWebhook;

        try {
            $api = new Api($apiKey, $secretKey);
            $api->utility->verifyWebhookSignature($post, $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'], $razorpayWebhookSecret);
        } catch (Errors\SignatureVerificationError | \Exception $e) {
            info($e->getMessage());

            return;
        }

        info('$_SERVER[HTTP_X_RAZORPAY_SIGNATURE]: '.$_SERVER['HTTP_X_RAZORPAY_SIGNATURE'].' $razorpayWebhookSecret: '.$razorpayWebhookSecret);
        info($requestData);

        return match ($requestData['event']) {
            self::SUBSCRIPTION_CHARGED => $this->paymentAuthorized($requestData),
            self::PAYMENT_FAILED => $this->paymentFailed(),
            self::SUBSCRIPTION_CANCELLED => $this->subscriptionCancelled($requestData),
            default => null,
        };
    }

    /**
     * Does nothing for the main payments flow currently
     */
    protected function paymentFailed(): bool
    {
        return false;
    }

    /**
     * Does nothing for the main payments flow currently
     * @param array $requestData Webook Data
     * @throws RelatedResourceNotFoundException
     */
    protected function subscriptionCancelled(array $requestData)
    {
        $subscriptionEndedAt = $requestData['payload']['subscription']['entity']['ended_at'];

        $razorpaySubscription = GlobalSubscription::where('gateway_name', 'razorpay')->where('subscription_status', 'active')->where('transaction_id', $requestData['payload']['subscription']['entity']['id'])->first();

        if (!is_null($razorpaySubscription)) {
            $razorpaySubscription->ends_at = Carbon::createFromTimestamp($subscriptionEndedAt)->format('Y-m-d');
            $razorpaySubscription->save();

            $razorpayInvoice = GlobalInvoice::where('gateway_name', 'razorpay')->where('transaction_id', $requestData['payload']['subscription']['entity']['id'])->first();
            $razorpayInvoice->next_pay_date = null;
            $razorpayInvoice->save();
        }

        return true;
    }

    /**
     * @param array $requestData
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function paymentAuthorized(array $requestData)
    {
        //
        // Order entity should be sent as part of the webhook payload
        //

        $packageId = $requestData['payload']['payment']['entity']['notes']['package_id'];
        $companyID = $requestData['payload']['payment']['entity']['notes']['company_id'];

        $plan = Package::find($packageId);
        $company = Company::findOrFail($companyID);

        $subscription = GlobalSubscription::where('gateway_name', 'razorpay')->where('company_id', $companyID)->where('package_id', $packageId)->first();

        // If it is already marked as paid, ignore the event
        $razorpayPaymentId = $requestData['payload']['payment']['entity']['id'];
        $credential = GlobalPaymentGatewayCredentials::first();

        if ($credential->razorpay_mode == 'test') {
            $apiKey = $credential->test_razorpay_key;
            $secretKey = $credential->test_razorpay_secret;
        }
        else {
            $apiKey = $credential->live_razorpay_key;
            $secretKey = $credential->live_razorpay_secret;
        }

        try {
            $api = new Api($apiKey, $secretKey);
            $payment = $api->payment->fetch($razorpayPaymentId);
        } catch (\Exception $e) {
            info($e->getMessage());

            return;
        }
        //
        // If the payment is only authorized, we capture it
        // If the merchant has enabled auto capture
        //
        try {

            $invoiceID = $requestData['payload']['payment']['entity']['invoice_id'];
            $orderID = $requestData['payload']['payment']['entity']['order_id'];
            $subscriptionID = $requestData['payload']['subscription']['entity']['id'];
            $customerID = $requestData['payload']['subscription']['entity']['customer_id'];
            $endTimeStamp = $requestData['payload']['subscription']['entity']['current_end'];
            $currencyCode = $requestData['payload']['payment']['entity']['currency'];
            $transactionId = $requestData['account_id'];
            $endDate = \Carbon\Carbon::createFromTimestamp($endTimeStamp)->format('Y-m-d');

            $currency = GlobalCurrency::where('currency_code', $currencyCode)->first();

            if ($currency) {
                $currencyID = $currency->id;
            }
            else {
                $currencyID = GlobalCurrency::where('currency_code', 'USD')->first()->id;
            }

            $razorpayInvoice = GlobalInvoice::where('gateway_name', 'razorpay')->where('invoice_id', $invoiceID)->first();

            // Store invoice details
            if (!$razorpayInvoice) {
                $razorpayInvoice = new GlobalInvoice();
            }

            $razorpayInvoice->company_id = $company->id;
            $razorpayInvoice->currency_id = $currencyID;
            $razorpayInvoice->order_id = $orderID;
            $razorpayInvoice->subscription_id = $subscriptionID;
            $razorpayInvoice->invoice_id = $invoiceID;
            $razorpayInvoice->transaction_id = $transactionId;
            $razorpayInvoice->amount = $payment->amount / 100;
            $razorpayInvoice->total = $payment->amount / 100;
            $razorpayInvoice->package_id = $packageId;
            $razorpayInvoice->pay_date = now()->format('Y-m-d');
            $razorpayInvoice->next_pay_date = $endDate;
            $razorpayInvoice->currency_id = $plan->currency_id;
            $razorpayInvoice->gateway_name = 'razorpay';
            $razorpayInvoice->global_subscription_id = $subscription->id;
            $razorpayInvoice->save();

            $subscription = GlobalSubscription::where('gateway_name', 'razorpay')->where('subscription_id', $subscriptionID)->first();
            $subscription->customer_id = $customerID;
            $subscription->save();

            // Change company status active after payment
            $company->status = 'active';
            $company->save();

            $generatedBy = User::whereNull('company_id')->get();
            $lastInvoice = RazorpayInvoice::first();

            if ($lastInvoice) {
                Notification::send($generatedBy, new CompanyUpdatedPlan($company, $plan->id));

            }
            else {
                Notification::send($generatedBy, new CompanyPurchasedPlan($company, $plan->id));

            }

            return response('Webhook Handled', 200);

        } catch (\Exception $e) {
            //
            // Capture will fail if the payment is already captured
            //
            $log = array(
                'message' => $e->getMessage(),
                'payment_id' => $razorpayPaymentId,
                'event' => $requestData['event']
            );
            error_log(json_encode($log));
        }

        // Graceful exit since payment is now processed.
        exit;

    }

}
