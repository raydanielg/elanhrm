<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUserEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $password;
    public $clientSignup;

    public function __construct(User $user, $password, $clientSignup = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->clientSignup = $clientSignup;
    }

}
