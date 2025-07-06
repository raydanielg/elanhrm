<?php

namespace App\Observers\SuperAdmin;

use App\Models\User;
use App\Models\SuperAdmin\SupportTicket;
use App\Events\SuperAdmin\NewSupportTicketEvent;
use App\Events\SuperAdmin\SupportTicketRequesterEvent;

class SupportTicketObserver
{

    public function created(SupportTicket $ticket)
    {
        if (!isRunningInConsoleOrSeeding()) {

            $users = User::allSuperAdmin();

            event(new NewSupportTicketEvent($ticket, $users));

            if ($ticket->requester && user()->id != $ticket->user_id) {
                event(new SupportTicketRequesterEvent($ticket, $ticket->requester));
            }
        }
    }

    public function updated(SupportTicket $ticket)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($ticket->isDirty('agent_id')) {
                event(new SupportTicketRequesterEvent($ticket, $ticket->agent));
            }
        }
    }

}
