<?php

namespace App\Listeners\SuperAdmin;

use Illuminate\Support\Facades\Notification;
use App\Events\SuperAdmin\SupportTicketRequesterEvent;
use App\Notifications\SuperAdmin\NewSupportTicketRequester;

class SupportTicketRequesterListener
{

    public function handle(SupportTicketRequesterEvent $event)
    {
        if (!is_null($event->notifyUser)) {
            Notification::send($event->notifyUser, new NewSupportTicketRequester($event->ticket));
        }
    }

}
