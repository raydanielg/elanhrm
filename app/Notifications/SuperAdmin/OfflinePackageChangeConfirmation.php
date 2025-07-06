<?php

namespace App\Notifications\SuperAdmin;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use App\Models\SuperAdmin\OfflinePlanChange;

class OfflinePackageChangeConfirmation extends BaseNotification
{

    use Queueable;

    private $planChange;
    private $forCompany;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(OfflinePlanChange $planChange, $company)
    {
        $this->planChange = $planChange;
        $this->forCompany = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
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
    // phpcs:ignore
    public function toMail($notifiable)
    {
        $mail = parent::build()
            ->subject(__('superadmin.offlinePackageRequestChange.subject'))
            ->greeting(__('email.hello') . '!')
            ->line(__('superadmin.offlinePackageRequestChange.text', ['status' => __('superadmin.offlineRequestStatus.' . $this->planChange->status), 'package' => $this->planChange->package->name . ' (' . $this->planChange->package_type . ')']));

        if ($this->planChange->status == 'rejected') {
            $mail = $mail->line(__('app.remark') . ': ' . $this->planChange->remark);
        }

        $mail = $mail->line(__('email.thankyouNote'));

        return $mail;
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
        return array_merge($this->planChange->toArray(), ['company_name' => $this->forCompany->company_name, 'package_name' => $this->planChange->package->name . ' (' . $this->planChange->package_type . ')']);
    }

}
