<?php

namespace App\Notifications\SuperAdmin;

use App\Models\Company;
use App\Models\SlackSetting;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\SlackMessage;

class CompanyApproved extends BaseNotification
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return parent::build()
            ->subject(__('superadmin.companyApproved.subject') . ' ' . config('app.name') . '!')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('superadmin.companyApproved.text'))
            ->line($this->forCompany->company_name)
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
        return array_merge($notifiable->toArray(), ['company_name' => $this->forCompany->company_name]);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = SlackSetting::first();

        if (count($notifiable->employee) > 0 && !is_null($notifiable->employee[0]->slack_username)) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('Welcome to ' . config('app.name') . '! New company has been registered.');
        }

        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
    }

}
