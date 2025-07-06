<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\Billing\AuthorizePaymentRequest;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\Package;
use Illuminate\Http\Request;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use net\authorize\api\constants\ANetEnvironment;

class AuthorizeController extends AccountBaseController
{

    public function createSubscription(AuthorizePaymentRequest $request)
    {
        $credential = GlobalPaymentGatewayCredentials::first();

        $package = Package::find($request->plan_id);

        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($credential->authorize_api_login_id);
        $merchantAuthentication->setTransactionKey($credential->authorize_transaction_key);

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName($package->name . ' ' . $request->type . ' Subscription');

        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();

        $packageType = $request->type;
        if ($request->package_type == 'lifetime') {
            $interval->setLength(1);
            $interval->setUnit('years');
        } elseif ($packageType == 'annual') {
            $interval->setLength(365);
            $interval->setUnit('days');
        } else {
            $interval->setLength(30);
            $interval->setUnit('days');
        }
        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new \DateTime(now()->format('Y-m-d')));
        $paymentSchedule->setTotalOccurrences($request->package_type == 'lifetime' ? '1' : '24');

        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($package->{$request->type . '_price'});
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($request->card_number);
        $creditCard->setExpirationDate($request->expiration_year . '-' . $request->expiration_month);

        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        $subscription->setPayment($payment);

        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber(date('YmdHis'));
        $order->setDescription($package->description);
        $subscription->setOrder($order);

        $billTo = new AnetAPI\NameAndAddressType();
        $billTo->setFirstName($request->name);
        $billTo->setLastName($request->name);

        $subscription->setBillTo($billTo);

        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);

        if ($credential->authorize_environment == 'sandbox') {

            $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
        } else {
            $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        }

        if (($response != null) && ($response->getMessages()->getResultCode() == 'Ok')) {
            $subscription = GlobalSubscription::where('gateway_name', 'authorize')->where('company_id', company()->id)->first();

            if (!$subscription) {
            $subscription = new GlobalSubscription();
            }

            $subscription->company_id = company()->id;
            $subscription->transaction_id = $response->getSubscriptionId();

            $subscription->package_id = $package->id;
            $subscription->package_type = $packageType;
            $subscription->gateway_name = 'authorize';
            $subscription->subscription_status = 'inactive';
            $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
            $subscription->save();

            \Session::put('success', __('superadmin.paymentProcessing', ['package' => $package->name, 'planType' => $packageType]));
            return Reply::redirect(route('billing.index'));
        } else {
            \session()->put('error', $response ? $response->getMessages()->getMessage()[0]->getText() : 'Something went wrong!');
            return Reply::redirect(route('billing.upgrade_plan'));
        }
    }

    public function checkSubscription(Request $request)
    {
        session()->forget('company');
        session()->forget('company_setting');

        $company = company();

        if ($company->package_id == $request->package_id && $company->package_type == $request->type) {
            return Reply::dataOnly(['status' => 'success', 'webhook' => true]);
        }

        return Reply::dataOnly(['status' => 'success', 'webhook' => false]);
    }

}
