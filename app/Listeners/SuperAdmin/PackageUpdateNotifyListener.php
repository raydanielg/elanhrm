<?php

namespace App\Listeners\SuperAdmin;

use App\Models\Company;
use Illuminate\Support\Facades\Notification;
use App\Events\SuperAdmin\PackageUpdateNotifyEvent;
use App\Notifications\SuperAdmin\PackageEmployeeIssue;

class PackageUpdateNotifyListener
{

    /**
     * Handle the event.
     */
    public function handle(PackageUpdateNotifyEvent $event)
    {
        $notifyUser = Company::firstActiveAdmin($event->packageUpdateNotify->company);
        Notification::send($notifyUser, new PackageEmployeeIssue($event->packageUpdateNotify));
    }

}
