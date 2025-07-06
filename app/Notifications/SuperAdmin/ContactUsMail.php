<?php

namespace App\Notifications\SuperAdmin;

use App\Notifications\BaseNotification;

class ContactUsMail extends BaseNotification
{


    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via()
    {
        $via = ['mail'];

        return $via;
    }

    public function toMail()
    {
        return parent::build()
            ->subject('Contact Us' . ' ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' Admin !')
            ->markdown('vendor.notifications.superadmin.contact-us', $this->data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $notifiable->toArray();
    }

}
