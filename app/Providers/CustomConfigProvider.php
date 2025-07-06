<?php

namespace App\Providers;

use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Traits\HasMaskImage;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

/**
 * This class is used to set the SMTP configuration, push notifications, session , driver
 * and translate setting. This is done via provider so as it works during supervisor also.
 * otherwise During supervisor the database configuration in controller do not work
 */
class CustomConfigProvider extends ServiceProvider
{

    use HasMaskImage;

    const ALL_ENVIRONMENT = ['demo', 'development', 'production'];

    public function register()
    {
        try {
            // Fetch all settings in a single query
            $setting = DB::table('smtp_settings')
                ->join('global_settings', function ($join) {
                    $join->on('global_settings.id', '=', DB::raw('global_settings.id'));
                })
                ->leftJoin('push_notification_settings', function ($join) {
                    $join->on('push_notification_settings.id', '=', DB::raw('push_notification_settings.id'));
                })
                ->leftJoin('translate_settings', function ($join) {
                    $join->on('translate_settings.id', '=', DB::raw('translate_settings.id'));
                })
                ->leftJoin('global_payment_gateway_credentials', function ($join) {
                    $join->on('global_payment_gateway_credentials.id', '=', DB::raw('global_payment_gateway_credentials.id'));
                })
                ->select(
                    'smtp_settings.*',
                    'global_settings.global_app_name',
                    'global_settings.session_driver',
                    'global_settings.timezone',
                    'global_settings.light_logo',
                    'push_notification_settings.onesignal_app_id',
                    'push_notification_settings.onesignal_rest_api_key',
                    'translate_settings.google_key',
                    'global_payment_gateway_credentials.stripe_mode',
                    'global_payment_gateway_credentials.test_stripe_client_id',
                    'global_payment_gateway_credentials.test_stripe_secret',
                    'global_payment_gateway_credentials.test_stripe_webhook_secret',
                    'global_payment_gateway_credentials.live_stripe_client_id',
                    'global_payment_gateway_credentials.live_stripe_secret',
                    'global_payment_gateway_credentials.live_stripe_webhook_secret'
                )
                ->first();


            if ($setting) {
                $this->setMailConfig($setting);
                $this->setPushNotification($setting);
                $this->setSessionDriver($setting);
                $this->translateSettingConfig($setting);
                $this->setStripConfigs($setting);

            }
        } catch (\Exception $e) {
            // info($e->getMessage());
            // Handle exceptions appropriately, e.g., log the error
        }

        $app = App::getInstance();
        $app->register(MailServiceProvider::class);
        $app->register(QueueServiceProvider::class);
        $app->register(SessionServiceProvider::class);
    }

    public function setMailConfig($setting)
    {
        if (!in_array(app()->environment(), self::ALL_ENVIRONMENT)) {
            $driver = ($setting->mail_driver != 'mail') ? $setting->mail_driver : 'sendmail';

            // Decrypt the password to be used
            $password = Crypt::decryptString($setting->mail_password);

            Config::set('mail.default', $driver);
            Config::set('mail.mailers.smtp.host', $setting->mail_host);
            Config::set('mail.mailers.smtp.port', $setting->mail_port);
            Config::set('mail.mailers.smtp.username', $setting->mail_username);
            Config::set('mail.mailers.smtp.password', $password);
            Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption);

            Config::set('mail.verified', (bool)$setting->email_verified);
            Config::set('queue.default', $setting->mail_connection);
        }

        Config::set('mail.from.name', $setting->mail_from_name);
        Config::set('mail.from.address', $setting->mail_from_email);

        Config::set('app.name', $setting->global_app_name);
        Config::set('app.global_app_name', $setting->global_app_name);
        Config::set('app.logo', is_null($setting->light_logo) ? asset('img/worksuite-logo.png') : $this->generateMaskedImageAppUrl('app-logo/' . $setting->light_logo));
    }

    public function setPushNotification($setting)
    {
        // Set push notification settings if available
        if ($setting->onesignal_app_id && $setting->onesignal_rest_api_key) {
            Config::set('services.onesignal.app_id', $setting->onesignal_app_id);
            Config::set('services.onesignal.rest_api_key', $setting->onesignal_rest_api_key);
            Config::set('onesignal.app_id', $setting->onesignal_app_id);
            Config::set('onesignal.rest_api_key', $setting->onesignal_rest_api_key);
        }
    }

    // SessionDriverConfigProvider moved here so it only fetches in single query
    public function setSessionDriver($setting)
    {
        Config::set('session.driver', $setting->session_driver != '' ? $setting->session_driver : 'file');
        Config::set('app.cron_timezone', $setting->timezone);
    }

    public function translateSettingConfig($setting)
    {
        Config::set('laravel_google_translate.google_translate_api_key', $setting->google_key);
    }


    public function setStripConfigs($setting)
    {
        if ($setting->stripe_mode === 'test') {

            $stripeClientId = $setting->test_stripe_client_id;
            $stripeSecret = $setting->test_stripe_secret;
            $stripeWebhookSecret = $setting->test_stripe_webhook_secret;
        }
        else {
            $stripeClientId = $setting->live_stripe_client_id;
            $stripeSecret = $setting->live_stripe_secret;
            $stripeWebhookSecret = $setting->live_stripe_webhook_secret;
        }

        $key = ($stripeClientId) ?: env('STRIPE_KEY');
        $apiSecret = ($stripeSecret) ?: env('STRIPE_SECRET');
        $webhookKey = ($stripeWebhookSecret) ?: env('STRIPE_WEBHOOK_SECRET');


        Config::set('cashier.key', $key);
        Config::set('cashier.secret', $apiSecret);
        Config::set('cashier.webhook.secret', $webhookKey);

        Stripe::setApiKey(config('cashier.secret'));

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

}
