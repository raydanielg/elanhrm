<?php

namespace App\Notifications\SuperAdmin;

use Illuminate\Bus\Queueable;
use App\Models\PackageUpdateNotify;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PackageEmployeeIssue extends BaseNotification
{

    use Queueable;

    public $packageUpdateNotify;

    /**
     * Create a new notification instance.
     */
    public function __construct(PackageUpdateNotify $packageUpdateNotify)
    {
        $this->packageUpdateNotify = $packageUpdateNotify;
        $this->company = $packageUpdateNotify->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        $via = [];

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
        $mail = parent::build()
            ->subject(__('superadmin.packageEmployeeIssueEmail.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('superadmin.packageEmployeeIssueEmail.text', ['userName' => $this->packageUpdateNotify->user->name]))
            ->action(__('email.loginDashboard'), getDomainSpecificUrl(route('dashboard'), $notifiable->company))
            ->line(__('email.thankyouNote'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

}
