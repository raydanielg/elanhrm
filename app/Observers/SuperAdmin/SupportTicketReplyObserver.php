<?php

namespace App\Observers\SuperAdmin;

use App\Models\SuperAdmin\SupportTicketReply;
use App\Events\SuperAdmin\SupportTicketReplyEvent;

class SupportTicketReplyObserver
{

    public function created(SupportTicketReply $ticketReply)
    {
        $ticketReply->ticket->touch();

        if (!isRunningInConsoleOrSeeding()) {
            if (count($ticketReply->ticket->reply) > 1) {
                if (!is_null($ticketReply->ticket->agent) && user()->id != $ticketReply->ticket->agent_id && user()->id == $ticketReply->ticket->user_id) {
                    event(new SupportTicketReplyEvent($ticketReply, $ticketReply->ticket->agent));
                }
                else if (is_null($ticketReply->ticket->agent) && user()->id == $ticketReply->ticket->user_id) {
                    event(new SupportTicketReplyEvent($ticketReply, null));
                }
                else {
                    event(new SupportTicketReplyEvent($ticketReply, $ticketReply->ticket->requester));
                }
            }
        }
    }

}
