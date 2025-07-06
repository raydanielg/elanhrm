<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class VerifyEmail extends BaseNotification
{

    private $verificationUrl;
    /**
     * The callback that should be used to create the verify email URL.
     *
     * @var \Closure|null
     */
    public static $createUrlCallback;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        $this->company = $notifiable->company;

        if (!$this->company) {
            $user = User::where('email', $notifiable->email)->latest()->first();
            $this->company = $user->company;
        }

        URL::forceRootUrl(getDomainSpecificUrl(request()->root(), $this->company));
        $this->verificationUrl = $this->verificationUrl($notifiable);

        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {


        $url = 'email/verify';
        $url = route('verification.notice');

        $url = getDomainSpecificUrl($url, $this->company);

        $this->company = $notifiable->company;

        $user = User::where('user_auth_id', $notifiable->id)->first();

        $emailVerifyCode = '<p style="color:#1d82f5"><strong>' . $notifiable->email_verification_code . '</strong></p>';

        $content = __('superadmin.emailVerificationCode.text') . '<br><br>' . __('superadmin.emailVerificationCode.line1') . '<br>' . new HtmlString($emailVerifyCode) . '<br>' . __('superadmin.emailVerificationCode.line2');

        $this->company = null;

        $build = parent::build();

        return $build
            ->subject(Lang::get('Email verification code') . ' ' . config('app.name'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company ? $this->company->header_color : null,
                'actionText' => __('superadmin.emailVerificationCode.action'),
                'notifiableName' => $user->name
            ]);
    }

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param string $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return $this->build()
            ->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $url)
            ->line(Lang::get('If you did not create an account, no further action is required.'))
            ->line(__('superadmin.verificationExpireIn', ['minutes' => Config::get('auth.verification.expire', 60)]));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(120),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return $url;
    }

    /**
     * Set a callback that should be used when creating the email verification URL.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
