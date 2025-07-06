<?php

namespace App\Events\SuperAdmin;

use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\OfflinePlanChange;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OfflinePackageChangeConfirmationEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offlinePlanChange;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OfflinePlanChange $offlinePlanChange)
    {
        $this->offlinePlanChange = $offlinePlanChange;
    }

}
