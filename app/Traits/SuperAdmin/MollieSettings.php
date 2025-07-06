<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits\SuperAdmin;

use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use Illuminate\Support\Facades\Config;

trait MollieSettings
{

    public function setMollieConfigs()
    {
        $settings = GlobalPaymentGatewayCredentials::first();
        $key       = ($settings->mollie_api_key) ?: env('MOLLIE_KEY');
        Config::set('mollie.key', $key);
        Config::set('mollie.api', $key);
    }

}



