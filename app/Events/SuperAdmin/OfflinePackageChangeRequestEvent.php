<?php

namespace App\Events\SuperAdmin;

use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\OfflinePlanChange;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OfflinePackageChangeRequestEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offlinePlanChange;
    public $company;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($company, OfflinePlanChange $offlinePlanChange)
    {
        $this->offlinePlanChange = $offlinePlanChange;
        $this->company = $company;
    }

}
