<?php

namespace App\Events\SuperAdmin;

use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\SupportTicket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewSupportTicketEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $notifyUser;

    public function __construct(SupportTicket $ticket, $notifyUser)
    {
        $this->ticket = $ticket;
        $this->notifyUser = $notifyUser;
    }

}
