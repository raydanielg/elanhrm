<?php

namespace App\Notifications\SuperAdmin;

use App\Models\Company;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TrialLicenseExp extends BaseNotification
{

    private $forCompany;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company)
    {
        $this->forCompany = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['database'];

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('superadmin.trialLicenseExp.subject') . ' - ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('superadmin.trialLicenseExp.text'))
            ->action(__('email.loginDashboard'), getDomainSpecificUrl(url('/login'), $this->forCompany))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $notifiable->toArray();
    }

}
