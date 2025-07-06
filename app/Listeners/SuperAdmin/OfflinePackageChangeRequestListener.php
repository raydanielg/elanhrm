<?php

namespace App\Listeners\SuperAdmin;

use App\Models\User;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Notification;
use App\Events\SuperAdmin\OfflinePackageChangeRequestEvent;
use App\Notifications\SuperAdmin\OfflinePackageChangeRequest;

class OfflinePackageChangeRequestListener
{

    /**
     * Handle the event.
     *
     * @param \App\Events\SuperAdmin\OfflinePackageChangeRequestEvent $event
     * @return void
     */
    public function handle(OfflinePackageChangeRequestEvent $event)
    {
        $generatedBy = User::withoutGlobalScope(CompanyScope::class)->whereNull('company_id')->get();
        Notification::send($generatedBy, new OfflinePackageChangeRequest($event->company, $event->offlinePlanChange));
    }

}
