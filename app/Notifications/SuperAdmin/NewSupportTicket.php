<?php

namespace App\Notifications\SuperAdmin;

use App\Models\SlackSetting;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use App\Models\PushNotificationSetting;
use App\Models\EmailNotificationSetting;
use App\Models\SuperAdmin\SupportTicket;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalChannel;

class NewSupportTicket extends BaseNotification
{

    use Queueable;

    private $ticket;
    private $emailSetting;
    private $pushNotification;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'New Support Ticket Request')->first();
        $this->pushNotification = PushNotificationSetting::first();
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

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes') {
            array_push($via, 'slack');
        }

        if ($this->emailSetting->send_push == 'yes' && $this->pushNotification->status == 'active') {
            array_push($via, OneSignalChannel::class);
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
            ->subject(__('superadmin.newSupportTicket.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('superadmin.newSupportTicket.text'))
            ->action(__('email.loginDashboard'), getDomainSpecificUrl(url('/login'), $notifiable->company))
            ->line(__('email.thankyouNote'));
    }

    // phpcs:ignore
    public function toArray($notifiable)
    {
        return $this->ticket->toArray();
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

        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image(asset('storage/slack-logo/' . $slack->slack_logo))
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('*' . __('superadmin.newSupportTicket.subject') . '*' . "\n" . $this->ticket->subject . "\n" . __('modules.tickets.requesterName') . ' - ' . $this->ticket->requester->name);
        }

        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
    }

    // phpcs:ignore
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.newTicket.subject'))
            ->body(__('email.newTicket.text'));
    }

}
