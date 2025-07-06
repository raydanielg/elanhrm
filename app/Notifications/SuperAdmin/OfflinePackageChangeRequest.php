<?php

namespace App\Notifications\SuperAdmin;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use App\Models\SuperAdmin\OfflinePlanChange;

class OfflinePackageChangeRequest extends BaseNotification
{

    use Queueable;

    private $planChange;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($company, OfflinePlanChange $planChange)
    {
        $this->planChange = $planChange;
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return parent::build()
            ->subject(__('superadmin.offlinePackageChangeRequest.subject', ['company' => $this->company->company_name]))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('superadmin.offlinePackageChangeRequest.text', ['company' => $this->company->company_name]))
            ->line(__('superadmin.offlinePackageChangeRequest.packageName') . ': ' . $this->planChange->package->name . ' (' . $this->planChange->package_type . ').')
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
    public function toArray($notifiable)
    {
        return array_merge($this->planChange->toArray(), ['company_name' => $this->company->company_name]);
    }

}
