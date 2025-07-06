<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Notifications\SuperAdmin\CompanyPurchasedPlan;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use App\Models\SuperAdmin\StripeInvoice;
use App\Models\SuperAdmin\Subscription;
use App\Traits\SuperAdmin\StripeSettings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controller;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use App\Models\SuperAdmin\Package;

class StripeWebhookController extends Controller
{

    use StripeSettings;

    public function verifyStripeWebhook(Request $request)
    {
        // Moved to service provider
        // $this->setStripConfigs();

        $stripeCredentials = config('cashier.webhook.secret');
        if (is_null($stripeCredentials)) {
            return response()->json([
                'error' => true,
                'message' => 'Webhook secret is not entered',
            ], 400); // 400 Bad Request
        }

        // Already set in customserviceprovider
        // Stripe::setApiKey(config('cashier.secret'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripeCredentials;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {

            Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );

        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid Payload' . $e->getMessage(), 400);

        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature' . $e->getMessage(), 400);

        }

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['data']['object']['object'])) {
            return response('Payload data not found', 200);
        }

        $customerId = $payload['data']['object']['customer'];
        $company = Company::where('stripe_id', $customerId)->first();

        if (!$company) {
            return response('Customer not found', 200);
        }
        // Do something with $event
        if ($payload['data']['object']['object'] == 'invoice') {

            if ($payload['type'] == 'invoice.payment_succeeded') {
                $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
                $invoice_number = $payload['data']['object']['number'];

                $amount = $payload['data']['object']['amount_paid'];
                $transactionId = $payload['data']['object']['lines']['data'][0]['id'];
                $invoiceRealId = $payload['data']['object']['id'];

                $package = Package::where(function ($query) use ($planId) {
                    $query->where('stripe_annual_plan_id', '=', $planId)
                        ->orWhere('stripe_monthly_plan_id', '=', $planId);
                })->first();

                $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')->where('company_id', $company->id)->latest()->first();
                $stripInvoiceData = GlobalInvoice::where('gateway_name', 'stripe')->where('company_id', $company->id)->where('transaction_id', $transactionId)->first();

                if (is_null($stripInvoiceData)) {
                    // Store invoice details
                    $stripeInvoice = new GlobalInvoice();
                    $stripeInvoice->global_subscription_id = $globalSubscription->id;
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->invoice_id = $invoiceRealId;
                    $stripeInvoice->transaction_id = $transactionId;
                    $stripeInvoice->amount = $amount / 100;
                    $stripeInvoice->total = $amount / 100;
                    $stripeInvoice->currency_id = $package->currency_id;
                    $stripeInvoice->package_type = $globalSubscription->package_type;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->pay_date = now()->format('Y-m-d');
                    $stripeInvoice->next_pay_date = ($company->upcomingInvoice()->next_payment_attempt) ? Carbon::createFromTimeStamp($company->upcomingInvoice()->next_payment_attempt)->format('Y-m-d') : '';
                    $stripeInvoice->stripe_invoice_number = $invoice_number;
                    $stripeInvoice->gateway_name = 'stripe';
                    $stripeInvoice->save();

                    // Change company status active after payment
                    $company->package_id = $package->id;
                    $company->package_type = $globalSubscription->package_type;

                    // Set company status active
                    $company->licence_expire_on = null;
                    $company->status = 'active';
                    $company->save();
                    $generatedBy = User::whereNull('company_id')->get();
                    $lastInvoice = StripeInvoice::where('company_id')->first();

                    if ($lastInvoice) {
                        Notification::send($generatedBy, new CompanyUpdatedPlan($company, $package->id));

                    }
                    else {
                        Notification::send($generatedBy, new CompanyPurchasedPlan($company, $package->id));

                    }
                }

                return response('Webhook Handled', 200);

            }

            if ($payload['type'] == 'invoice.payment_failed') {

                $subscription = Subscription::where('company_id', $company->id)->first();
                $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')->where('company_id', $company->id)->first();

                if ($subscription && isset($payload['data']['object']['current_period_end'])) {
                    $subscription->ends_at = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $globalSubscription->ends_at = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $globalSubscription->save();
                    $subscription->save();
                }

                if (isset($payload['data']['object']['current_period_end'])) {
                    $company->licence_expire_on = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $company->save();

                    return response('Company subscription canceled', 200);
                }

                return response('Customer not found', 200);
            }
        }

        // If webhook with payment_intent (Success or Failed)
        if ($payload['data']['object']['object'] == 'payment_intent') {

            $subscription = Subscription::where('company_id', $company->id)->latest()->first();
            $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')->where('company_id', $company->id)->first();


            if ($payload['type'] == 'payment_intent.succeeded') {

                if ($subscription) {
                    $subscription->stripe_status = 'active';
                    $globalSubscription->stripe_status = 'active';
                    $globalSubscription->save();
                    $subscription->save();
                }

                return response('Webhook Handled', 200);
            }

            if ($payload['type'] == 'payment_intent.payment_failed') {

                if ($subscription) {

                    if (isset($payload['data']['object']['current_period_end'])) {
                        $subscription->ends_at = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                        $globalSubscription->ends_at = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    }

                    $globalSubscription->save();
                    $subscription->save();
                }

                if (isset($payload['data']['object']['current_period_end'])) {
                    $company->licence_expire_on = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $company->save();
                }

                return response('intent failed', 400);

            }

        }

    }

}
