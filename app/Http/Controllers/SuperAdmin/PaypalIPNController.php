<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Company;
use App\Models\Notification;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Notifications\SuperAdmin\CompanyPurchasedPlan;
use App\Notifications\SuperAdmin\CompanyUpdatedPlan;
use App\Models\SuperAdmin\PaypalInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaypalIPNController extends Controller
{

    // phpcs:ignore
    public function verifyBillingIPN(Request $request)
    {

        $txnType = $request->get('txn_type');

        if ($txnType == 'recurring_payment') {

            $recurringPaymentId = $request->get('recurring_payment_id');
            $eventId = $request->get('ipn_track_id');

            $event = GlobalInvoice::where('gateway_name', 'paypal')->where('event_id', $eventId)->count();

            if($event == 0)
            {
                $payment = GlobalInvoice::where('gateway_name', 'paypal')->where('transaction_id', $recurringPaymentId)->first();
                $today = now();

                if($payment->package_type == 'annual') {
                    $nextPaymentDate = $today->addYear();

                } else if(company()->package_type == 'monthly') {
                    $nextPaymentDate = $today->addMonth();

                }

                $paypalInvoice = new GlobalInvoice();
                $paypalInvoice->transaction_id = $recurringPaymentId;
                $paypalInvoice->company_id = $payment->company_id;
                $paypalInvoice->currency_id = $payment->currency_id;
                $paypalInvoice->total = $payment->total;
                $paypalInvoice->status = 'paid';
                $paypalInvoice->plan_id = $payment->plan_id;
                $paypalInvoice->billing_frequency = $payment->billing_frequency;
                $paypalInvoice->event_id = $eventId;
                $paypalInvoice->billing_interval = 1;
                $paypalInvoice->paid_on = $today;
                $paypalInvoice->next_pay_date = $nextPaymentDate;
                $paypalInvoice->global_subscription_id = $payment->global_subscription_id;
                $paypalInvoice->save();

                // Change company status active after payment
                $company = Company::findOrFail($payment->company_id);
                $company->status = 'active';
                $company->save();

                $generatedBy = User::whereNull('company_id')->get();
                $lastInvoice = PaypalInvoice::where('company_id')->count();

                if($lastInvoice > 1){
                    Notification::send($generatedBy, new CompanyUpdatedPlan($company, $payment->plan_id));

                }else{
                    Notification::send($generatedBy, new CompanyPurchasedPlan($company, $payment->plan_id));

                }

                return response('IPN Handled', 200);
            }

        }

    }

}
