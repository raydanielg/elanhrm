<?php

namespace App\Listeners;

use App\Events\EventCompletedEvent;
use App\Models\User;
use App\Notifications\EventCompleted;
use Illuminate\Support\Facades\Notification;

class EventCompletedListener
{

    /**
     * Handle the event.
     */

    public function handle(EventCompletedEvent $event)
    {
        $notifyUsers = $event->notifyUser->filter(function ($user) use ($event) {
            return $user->id !== $event->event->host;
        });

        $host = User::find($event->event->host);

        Notification::send($notifyUsers, new EventCompleted($event->event));

        if ($host) {
            Notification::send($host, new EventCompleted($event->event));
        }

    }

}
