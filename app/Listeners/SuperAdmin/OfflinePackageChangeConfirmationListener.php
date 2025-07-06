<?php

namespace App\Listeners\SuperAdmin;

use App\Events\SuperAdmin\OfflinePackageChangeConfirmationEvent;
use App\Models\Company;
use App\Notifications\SuperAdmin\OfflinePackageChangeConfirmation;
use Illuminate\Support\Facades\Notification;

class OfflinePackageChangeConfirmationListener
{

    /**
     * Handle the event.
     *
     * @param \App\Events\SuperAdmin\OfflinePackageChangeConfirmationEvent $event
     * @return void
     */
    public function handle(OfflinePackageChangeConfirmationEvent $event)
    {
        $notifyUser = Company::firstActiveAdmin($event->offlinePlanChange->company);
        Notification::send($notifyUser, new OfflinePackageChangeConfirmation($event->offlinePlanChange, $event->offlinePlanChange->company));
    }

}
