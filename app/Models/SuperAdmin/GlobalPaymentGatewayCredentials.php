<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

/**
 * App\Models\StripeSetting
 *
 * @property int $id
 * @property string|null $api_key
 * @property string|null $api_secret
 * @property string|null $webhook_key
 * @property string|null $paypal_client_id
 * @property string|null $paypal_secret
 * @property string $paypal_status
 * @property string $stripe_status
 * @property string|null $razorpay_key
 * @property string|null $razorpay_secret
 * @property string|null $razorpay_webhook_secret
 * @property string $razorpay_status
 * @property string $paypal_mode
 * @property string|null $paystack_client_id
 * @property string|null $paystack_secret
 * @property string|null $paystack_status
 * @property string|null $paystack_merchant_email
 * @property string|null $paystack_payment_url
 * @property string $mollie_api_key
 * @property string $mollie_status
 * @property string|null $authorize_api_login_id
 * @property string|null $authorize_transaction_key
 * @property string|null $authorize_signature_key
 * @property string|null $authorize_environment
 * @property string $authorize_status
 * @property string|null $payfast_key
 * @property string|null $payfast_secret
 * @property string $payfast_status
 * @property string|null $payfast_salt_passphrase
 * @property string $payfast_mode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $show_pay
 * @method static Builder|StripeSetting newModelQuery()
 * @method static Builder|StripeSetting newQuery()
 * @method static Builder|StripeSetting query()
 * @method static Builder|StripeSetting whereApiKey($value)
 * @method static Builder|StripeSetting whereApiSecret($value)
 * @method static Builder|StripeSetting whereAuthorizeApiLoginId($value)
 * @method static Builder|StripeSetting whereAuthorizeEnvironment($value)
 * @method static Builder|StripeSetting whereAuthorizeSignatureKey($value)
 * @method static Builder|StripeSetting whereAuthorizeStatus($value)
 * @method static Builder|StripeSetting whereAuthorizeTransactionKey($value)
 * @method static Builder|StripeSetting whereCreatedAt($value)
 * @method static Builder|StripeSetting whereId($value)
 * @method static Builder|StripeSetting whereMollieApiKey($value)
 * @method static Builder|StripeSetting whereMollieStatus($value)
 * @method static Builder|StripeSetting wherePayfastKey($value)
 * @method static Builder|StripeSetting wherePayfastMode($value)
 * @method static Builder|StripeSetting wherePayfastSaltPassphrase($value)
 * @method static Builder|StripeSetting wherePayfastSecret($value)
 * @method static Builder|StripeSetting wherePayfastStatus($value)
 * @method static Builder|StripeSetting wherePaypalClientId($value)
 * @method static Builder|StripeSetting wherePaypalMode($value)
 * @method static Builder|StripeSetting wherePaypalSecret($value)
 * @method static Builder|StripeSetting wherePaypalStatus($value)
 * @method static Builder|StripeSetting wherePaystackClientId($value)
 * @method static Builder|StripeSetting wherePaystackMerchantEmail($value)
 * @method static Builder|StripeSetting wherePaystackPaymentUrl($value)
 * @method static Builder|StripeSetting wherePaystackSecret($value)
 * @method static Builder|StripeSetting wherePaystackStatus($value)
 * @method static Builder|StripeSetting whereRazorpayKey($value)
 * @method static Builder|StripeSetting whereRazorpaySecret($value)
 * @method static Builder|StripeSetting whereRazorpayStatus($value)
 * @method static Builder|StripeSetting whereRazorpayWebhookSecret($value)
 * @method static Builder|StripeSetting whereStripeStatus($value)
 * @method static Builder|StripeSetting whereUpdatedAt($value)
 * @method static Builder|StripeSetting whereWebhookKey($value)
 * @mixin Eloquent
 */
class GlobalPaymentGatewayCredentials extends BaseModel
{

    use HasFactory;

    protected $table = 'global_payment_gateway_credentials';

}
