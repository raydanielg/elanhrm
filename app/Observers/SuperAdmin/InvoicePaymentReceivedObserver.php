<?php

namespace App\Observers\SuperAdmin;

use App\Models\SuperAdmin\ClientPayment;
use App\Models\Invoice;
use App\Notifications\InvoicePaymentReceived;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Notification;

class InvoicePaymentReceivedObserver
{

    public function created(ClientPayment $payment)
    {
        if (isRunningInConsoleOrSeeding()) {
            return true;
        }

        try {

            $admins = User::frontAllAdmins($payment->company_id);
            $invoice = Invoice::findOrFail($payment->invoice_id);

            if ($invoice && in_array($invoice->status, ['paid', 'partial'])) {
                Notification::send($admins, new InvoicePaymentReceived($invoice));
            }


        } catch (Exception $e) {
            info($e->getMessage());
        }

    }

}
