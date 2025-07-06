<?php

namespace App\Listeners\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Events\SuperAdmin\SupportTicketReplyEvent;
use App\Notifications\SuperAdmin\NewSupportTicketReply;

class SupportTicketReplyListener
{

    /**
     * Handle the event.
     *
     * @param \App\Events\SuperAdmin\SupportTicketReplyEvent $event
     * @return void
     */
    public function handle(SupportTicketReplyEvent $event)
    {
        if (!is_null($event->notifyUser)) {
            Notification::send($event->notifyUser, new NewSupportTicketReply($event->ticketReply));
        }
        else {
            Notification::send(User::allSuperAdmin(), new NewSupportTicketReply($event->ticketReply));
        }
    }

}
