<?php

namespace App\Events\SuperAdmin;

use App\Models\PackageUpdateNotify;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PackageUpdateNotifyEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $packageUpdateNotify;

    /**
     * Create a new event instance.
     */
    public function __construct(PackageUpdateNotify $packageUpdateNotify)
    {
        $this->packageUpdateNotify = $packageUpdateNotify;
    }

}
