<?php

namespace App\Events;

use App\Models\UserAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    //phpcs:ignore
    public function __construct(public UserAuth $user, public $ip)
    {
        //
    }

}
