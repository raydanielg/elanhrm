<?php

namespace App\Events\SuperAdmin;

use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\SupportTicketReply;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SupportTicketReplyEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketReply;
    public $notifyUser;

    public function __construct(SupportTicketReply $ticketReply, $notifyUser)
    {
        $this->ticketReply = $ticketReply;
        $this->notifyUser = $notifyUser;
    }

}
