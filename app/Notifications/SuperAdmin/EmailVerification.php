<?php

namespace App\Notifications\SuperAdmin;

use App\Models\User;
use App\Notifications\BaseNotification;

class EmailVerification extends BaseNotification
{

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *t('mail::layout')
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
    public function via($notifiable)
    {
        $via = ['mail'];

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // phpcs:ignore
    public function toMail($notifiable)
    {
        return parent::build()
            ->subject(__('email.emailVerify.subject') . ' ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' ' . $this->user->name . '!')
            ->line(__('email.emailVerify.text'))
            ->action('Verify', getDomainSpecificUrl(route('front.get-email-verification', $this->user->email_verification_code), $this->user->company))
            ->line(__('email.thankyouNote'));
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
