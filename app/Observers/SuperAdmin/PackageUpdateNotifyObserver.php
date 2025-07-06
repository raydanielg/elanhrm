<?php

namespace App\Observers\SuperAdmin;

use App\Models\PackageUpdateNotify;
use App\Events\SuperAdmin\PackageUpdateNotifyEvent;

class PackageUpdateNotifyObserver
{

    public function created(PackageUpdateNotify $packageUpdateNotify)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new PackageUpdateNotifyEvent($packageUpdateNotify));
        }
    }

}
