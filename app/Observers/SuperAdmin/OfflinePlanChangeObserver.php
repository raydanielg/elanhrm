<?php

namespace App\Observers\SuperAdmin;

use App\Models\SuperAdmin\OfflinePlanChange;
use App\Events\SuperAdmin\OfflinePackageChangeRequestEvent;
use App\Events\SuperAdmin\OfflinePackageChangeConfirmationEvent;

class OfflinePlanChangeObserver
{

    public function created(OfflinePlanChange $offlinePlanChange)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $company = company();
            event(new OfflinePackageChangeRequestEvent($company, $offlinePlanChange));
        }
    }

    public function updated(OfflinePlanChange $offlinePlanChange)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($offlinePlanChange->isDirty('status')) {
                event(new OfflinePackageChangeConfirmationEvent($offlinePlanChange));
            }
        }
    }

}
