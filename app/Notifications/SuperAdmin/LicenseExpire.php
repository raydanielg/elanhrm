<?php

namespace App\Notifications\SuperAdmin;

use App\Models\Company;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LicenseExpire extends BaseNotification
{

    private $forCompany;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(Company $company)
    {
        $this->forCompany = $company;
    }

    /**
     * Get the notification's delivery channels.
     *t('mail::layout')
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('superadmin.licenseExpire.subject') . ' ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name)
            ->line(__('superadmin.licenseExpire.text'))
            ->action(__('email.loginDashboard'), getDomainSpecificUrl(url('/login'), $this->forCompany))
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
