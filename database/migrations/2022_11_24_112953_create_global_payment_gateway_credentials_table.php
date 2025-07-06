<?php

use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('global_payment_gateway_credentials')) {
            Schema::create('global_payment_gateway_credentials', function (Blueprint $table) {
                $table->increments('id');
                $table->string('paypal_client_id')->nullable();
                $table->string('paypal_secret')->nullable();
                $table->string('sandbox_paypal_client_id')->nullable();
                $table->string('sandbox_paypal_secret')->nullable();
                $table->enum('paypal_status', ['active', 'deactive'])->default('deactive');
                $table->enum('paypal_mode', ['sandbox', 'live'])->default('sandbox');

                $table->string('live_stripe_client_id')->nullable();
                $table->string('live_stripe_secret')->nullable();
                $table->string('live_stripe_webhook_secret')->nullable();
                $table->string('test_stripe_client_id')->nullable();
                $table->string('test_stripe_secret')->nullable();
                $table->string('test_stripe_webhook_secret')->nullable();
                $table->enum('stripe_status', ['active', 'deactive'])->default('deactive');
                $table->enum('stripe_mode', ['test', 'live'])->default('test');

                $table->string('live_razorpay_key')->nullable();
                $table->string('live_razorpay_secret')->nullable();
                $table->string('test_razorpay_key')->nullable();
                $table->string('test_razorpay_secret')->nullable();
                $table->enum('razorpay_status', ['active', 'inactive'])->default('inactive');
                $table->enum('razorpay_mode', ['test', 'live'])->default('test');

                $table->string('paystack_key')->nullable();
                $table->string('paystack_secret')->nullable();
                $table->string('paystack_merchant_email')->nullable();
                $table->string('test_paystack_key')->nullable();
                $table->string('test_paystack_secret')->nullable();
                $table->string('test_paystack_merchant_email')->nullable();
                $table->string('paystack_payment_url')->nullable()->default('https://api.paystack.co');
                $table->enum('paystack_status', ['active', 'deactive'])->nullable()->default('deactive');
                $table->enum('paystack_mode', ['sandbox', 'live'])->default('sandbox');

                $table->string('mollie_api_key')->nullable();
                $table->enum('mollie_status', ['active', 'deactive'])->nullable()->default('deactive');

                $table->string('payfast_merchant_id')->nullable();
                $table->string('payfast_merchant_key')->nullable();
                $table->string('payfast_passphrase')->nullable();
                $table->string('test_payfast_merchant_id')->nullable();
                $table->string('test_payfast_merchant_key')->nullable();
                $table->string('test_payfast_passphrase')->nullable();
                $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');
                $table->enum('payfast_status', ['active', 'deactive'])->nullable()->default('deactive');

                $table->string('authorize_api_login_id')->nullable();
                $table->string('authorize_transaction_key')->nullable();
                $table->enum('authorize_environment', ['sandbox', 'live'])->default('sandbox');
                $table->enum('authorize_status', ['active', 'deactive'])->default('deactive');

                $table->string('square_application_id')->nullable();
                $table->string('square_access_token')->nullable();
                $table->string('square_location_id')->nullable();
                $table->enum('square_environment', ['sandbox', 'production'])->default('sandbox');
                $table->enum('square_status', ['active', 'deactive'])->default('deactive');

                $table->string('test_flutterwave_key')->nullable();
                $table->string('test_flutterwave_secret')->nullable();
                $table->string('test_flutterwave_hash')->nullable();
                $table->string('live_flutterwave_key')->nullable();
                $table->string('live_flutterwave_secret')->nullable();
                $table->string('live_flutterwave_hash')->nullable();
                $table->string('flutterwave_webhook_secret_hash')->nullable();
                $table->enum('flutterwave_status', ['active', 'deactive'])->default('deactive');
                $table->enum('flutterwave_mode', ['sandbox', 'live'])->default('sandbox');

                $table->timestamps();
            });
        }

        $oldCredentials = DB::table('stripe_setting')->first();

        if($oldCredentials){
            $globalPaymentGatewayCredentials = new GlobalPaymentGatewayCredentials();

            $globalPaymentGatewayCredentials->live_stripe_client_id = $oldCredentials->api_key;
            $globalPaymentGatewayCredentials->live_stripe_secret = $oldCredentials->api_secret;
            $globalPaymentGatewayCredentials->live_stripe_webhook_secret = $oldCredentials->webhook_key;
            $globalPaymentGatewayCredentials->stripe_status = $oldCredentials->stripe_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->stripe_mode = 'live';

            $globalPaymentGatewayCredentials->paypal_client_id = $oldCredentials->paypal_client_id;
            $globalPaymentGatewayCredentials->paypal_secret = $oldCredentials->paypal_secret;
            $globalPaymentGatewayCredentials->paypal_status = $oldCredentials->paypal_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->paypal_mode = 'live';

            $globalPaymentGatewayCredentials->live_razorpay_key = $oldCredentials->razorpay_key;
            $globalPaymentGatewayCredentials->live_razorpay_secret = $oldCredentials->razorpay_secret;
            $globalPaymentGatewayCredentials->razorpay_status = $oldCredentials->razorpay_status;
            $globalPaymentGatewayCredentials->razorpay_mode = 'live';

            $globalPaymentGatewayCredentials->paystack_key = $oldCredentials->paystack_client_id;
            $globalPaymentGatewayCredentials->paystack_secret = $oldCredentials->paystack_secret;
            $globalPaymentGatewayCredentials->paystack_merchant_email = $oldCredentials->paystack_merchant_email;
            $globalPaymentGatewayCredentials->paystack_payment_url = $oldCredentials->paystack_payment_url;
            $globalPaymentGatewayCredentials->paystack_status = $oldCredentials->paystack_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->paystack_mode = 'live';

            $globalPaymentGatewayCredentials->payfast_merchant_id = $oldCredentials->payfast_key;
            $globalPaymentGatewayCredentials->payfast_merchant_key = $oldCredentials->payfast_secret;
            $globalPaymentGatewayCredentials->payfast_passphrase = $oldCredentials->payfast_salt_passphrase;
            $globalPaymentGatewayCredentials->payfast_status = $oldCredentials->payfast_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->payfast_mode = 'live';

            $globalPaymentGatewayCredentials->authorize_api_login_id = $oldCredentials->authorize_api_login_id;
            $globalPaymentGatewayCredentials->authorize_transaction_key = $oldCredentials->authorize_transaction_key;
            $globalPaymentGatewayCredentials->authorize_status = $oldCredentials->authorize_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->authorize_environment = 'live';

            $globalPaymentGatewayCredentials->mollie_api_key = $oldCredentials->mollie_api_key;
            $globalPaymentGatewayCredentials->mollie_status = $oldCredentials->mollie_status == 'active' ? 'active' : 'deactive';
            $globalPaymentGatewayCredentials->save();
        }

        if (!Schema::hasTable('mollie_subscriptions')) {
            Schema::create('mollie_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('customer_id')->nullable();
                $table->string('subscription_id')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_payment_gateway_credentials');
    }

};
