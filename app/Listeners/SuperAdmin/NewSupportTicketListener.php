<?php

namespace App\Listeners\SuperAdmin;

use Illuminate\Support\Facades\Notification;
use App\Events\SuperAdmin\NewSupportTicketEvent;
use App\Notifications\SuperAdmin\NewSupportTicket;

class NewSupportTicketListener
{

    /**
     * Handle the event.
     *
     * @param \App\Events\SuperAdmin\NewSupportTicketEvent $event
     * @return void
     */
    public function handle(NewSupportTicketEvent $event)
    {
        if (!is_null($event->notifyUser)) {
            Notification::send($event->notifyUser, new NewSupportTicket($event->ticket));
        }
    }

}
