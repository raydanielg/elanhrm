<?php

namespace App\Observers;

use App\Models\OfflinePaymentMethod;

class OfflinePaymentMethodObserver
{

    public function creating(OfflinePaymentMethod $offlinePaymentMethod)
    {
        if (company()) {
            $offlinePaymentMethod->company_id = company()->id;
        }
    }

}
