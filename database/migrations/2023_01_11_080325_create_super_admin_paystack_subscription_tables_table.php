<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SuperAdmin\Subscription;
use Illuminate\Support\Facades\Artisan;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Models\SuperAdmin\MollieInvoice;
use App\Models\SuperAdmin\PaypalInvoice;
use App\Models\SuperAdmin\StripeInvoice;
use App\Models\SuperAdmin\OfflineInvoice;
use App\Models\SuperAdmin\PayfastInvoice;
use App\Models\SuperAdmin\PaystackInvoice;
use App\Models\SuperAdmin\RazorpayInvoice;
use App\Models\SuperAdmin\AuthorizeInvoice;
use App\Models\SuperAdmin\GlobalSubscription;
use App\Models\SuperAdmin\MollieSubscription;
use Illuminate\Database\Migrations\Migration;
use App\Models\SuperAdmin\PayfastSubscription;
use App\Models\SuperAdmin\GlobalInvoiceSetting;
use App\Models\SuperAdmin\PaystackSubscription;
use App\Models\SuperAdmin\RazorpaySubscription;
use App\Models\SuperAdmin\AuthorizeSubscription;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('pusher_settings', 'company_id')) {
            Schema::table('pusher_settings', function ($table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (isWorksuiteSaas()) {
            Artisan::call('fix:company');
        }

        if (!Schema::hasColumn('global_payment_gateway_credentials', 'razorpay_webhook_secret')) {
            Schema::table('global_payment_gateway_credentials', function ($table) {
                $table->string('razorpay_webhook_secret')->nullable()->after('test_razorpay_secret');
            });
        }


        if (!Schema::hasTable('paystack_subscriptions')) {
            Schema::create('paystack_subscriptions', function ($table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('subscription_id')->nullable();
                $table->string('customer_id')->nullable();
                $table->string('token');
                $table->string('plan_id');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('authorize_subscriptions')) {
            Schema::create('authorize_subscriptions', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('subscription_id');
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->foreign('plan_id')->references('id')->on('packages')->onDelete('cascade')->onUpdate('cascade');
                $table->string('plan_type')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payfast_subscriptions')) {
            Schema::create('payfast_subscriptions', function ($table) {
                $table->id();
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('payfast_plan')->nullable();
                $table->integer('quantity')->nullable();
                $table->enum('payfast_status', ['active', 'inactive'])->default('inactive');
                $table->string('ends_at')->nullable();
                $table->timestamps();
            });
        }

        DB::statement('ALTER TABLE companies MODIFY status ENUM("active", "inactive", "license_expired") DEFAULT "active"');


        if (Schema::hasTable('global_currencies')) {
            Schema::table('global_settings', function ($table) {
                $table->dropForeign(['currency_id']);
            });

            Schema::table('packages', function ($table) {
                $table->dropForeign(['currency_id']);
            });

            Schema::table('global_currencies', function ($table) {
                $table->id()->change();
            });
            Schema::table('global_settings', function ($table) {
                $table->unsignedBigInteger('currency_id')->nullable()->default(null)->change();
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('global_currencies')
                    ->onDelete(null)
                    ->onUpdate('cascade');
            });
            Schema::table('packages', function ($table) {
                $table->unsignedBigInteger('currency_id')->nullable()->default(null)->change();
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('global_currencies')
                    ->onDelete(null)
                    ->onUpdate('cascade');
            });
        }

        if (!Schema::hasTable('global_subscriptions')) {
            Schema::create('global_subscriptions', function ($table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('currency_id')->nullable();
                $table->foreign('currency_id')->references('id')->on('global_currencies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('package_type')->nullable();
                $table->string('plan_type')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('name')->nullable();
                $table->string('customer_id')->nullable();
                $table->string('user_id')->nullable();
                $table->string('payfast_plan')->nullable();
                $table->string('payfast_status')->nullable();
                $table->string('quantity')->nullable();
                $table->string('token')->nullable();
                $table->string('razorpay_id')->nullable();
                $table->string('razorpay_plan')->nullable();
                $table->string('stripe_id')->nullable();
                $table->string('stripe_status')->nullable();
                $table->string('stripe_price')->nullable();
                $table->string('gateway_name')->nullable();
                $table->string('trial_ends_at')->nullable();
                $table->enum('subscription_status', ['active', 'inactive'])->nullable()->default(null);
                $table->dateTime('ends_at')->nullable();
                $table->dateTime('subscribed_on_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('global_invoices')) {
            Schema::create('global_invoices', function ($table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('currency_id')->nullable();
                $table->foreign('currency_id')->references('id')->on('global_currencies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('global_subscription_id')->unsigned()->nullable();
                $table->foreign('global_subscription_id')->references('id')->on('global_subscriptions')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('offline_method_id')->unsigned()->nullable();
                $table->foreign('offline_method_id')->references('id')->on('offline_payment_methods')->onDelete('cascade')->onUpdate('cascade');
                $table->string('m_payment_id')->nullable();
                $table->string('pf_payment_id')->nullable();
                $table->string('payfast_plan')->nullable();
                $table->string('signature')->nullable();
                $table->string('token')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('package_type')->nullable();
                $table->integer('sub_total')->nullable();
                $table->integer('total')->nullable();
                $table->string('billing_frequency')->nullable();
                $table->string('billing_interval')->nullable();
                $table->enum('recurring', ['yes', 'no'])->nullable()->default(null);
                $table->string('plan_id')->nullable();
                $table->string('event_id')->nullable();
                $table->string('order_id')->nullable();
                $table->string('subscription_id')->nullable();
                $table->string('invoice_id')->nullable();
                $table->double('amount')->nullable();
                $table->string('stripe_invoice_number')->nullable();
                $table->dateTime('pay_date')->nullable();
                $table->dateTime('next_pay_date')->nullable();
                $table->string('gateway_name')->nullable();
                $table->enum('status', ['active', 'inactive'])->nullable()->default(null);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('global_invoice_settings')) {
            Schema::create('global_invoice_settings', function ($table) {
                $table->id();
                $table->string('logo')->nullable();
                $table->string('template');
                $table->string('locale')->nullable()->default('en');
                $table->boolean('authorised_signatory')->default(false);
                $table->string('authorised_signatory_signature')->nullable();
                $table->text('invoice_terms');
                $table->timestamps();
            });

            $invoiceSetting = new GlobalInvoiceSetting();
            $invoiceSetting->template = 'invoice-5';
            $invoiceSetting->invoice_terms = 'Thank you for your business.';
            $invoiceSetting->save();
        }

        $this->globalSubscriptions();
        $this->globalInvoices();

        if(Schema::hasColumn('offline_plan_changes', 'invoice_id')){
            Schema::table('offline_plan_changes', function ($table) {
                $table->unsignedBigInteger('invoice_id')->nullable()->change();
            });
        }

        if(!Schema::hasColumn('offline_plan_changes', 'amount')){
            Schema::table('offline_plan_changes', function ($table) {
                $table->double('amount')->nullable()->after('package_type');
                $table->date('pay_date')->nullable()->after('amount');
                $table->date('next_pay_date')->nullable()->after('pay_date');
                $table->text('remark')->nullable()->after('status');
            });
        }

    }

    private function globalSubscriptions()
    {
        $subscriptions = [];

        foreach (AuthorizeSubscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'subscription_id' => $modal->subscription_id,
                'package_id' => $modal->plan_id,
                'package_type' => $modal->plan_type,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'authorize',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (MollieSubscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'subscription_id' => $modal->subscription_id,
                'customer_id' => $modal->customer_id,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'mollie',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (PayfastSubscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'payfast_plan' => $modal->payfast_plan,
                'quantity' => $modal->quantity,
                'payfast_status' => $modal->payfast_status,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'payfast',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (PaystackSubscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'payfast_plan' => $modal->payfast_plan,
                'customer_id' => $modal->customer_id,
                'payfast_status' => $modal->payfast_status,
                'token' => $modal->token,
                'package_id' => $modal->plan_id,
                'status' => $modal->status,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'paystack',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (RazorpaySubscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'subscription_id' => $modal->subscription_id,
                'customer_id' => $modal->customer_id,
                'name' => $modal->name,
                'razorpay_id' => $modal->razorpay_id,
                'razorpay_plan' => $modal->razorpay_plan,
                'quantity' => $modal->quantity,
                'trial_ends_at' => $modal->trial_ends_at,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'razorpay',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (Subscription::all() as $modal){
            $subscriptions[] = [
                'company_id' => $modal->company_id,
                'name' => $modal->name,
                'stripe_id' => $modal->stripe_id,
                'stripe_plan' => $modal->stripe_plan,
                'quantity' => $modal->quantity,
                'trial_ends_at' => $modal->trial_ends_at,
                'stripe_status' => $modal->stripe_status,
                'ends_at' => $modal->ends_at,
                'gateway_name' => 'stripe',
                'subscription_status' => 'active',
                'subscribed_on_date' => $modal->created_at,
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach ($subscriptions as $subscription){

            GlobalSubscription::create($subscription);
        }


    }

    private function globalInvoices()
    {
        $invoices = [];

        foreach (AuthorizeInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->package->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id,
                'transaction_id' => $modal->transaction_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'gateway_name' => 'authorize',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (MollieInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->package->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id,
                'transaction_id' => $modal->transaction_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'package_type' => $modal->package_type,
                'gateway_name' => 'mollie',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (OfflineInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'transaction_id' => $modal->transaction_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'package_type' => $modal->package_type,
                'offline_method_id' => $modal->offline_method_id,
                'status' => $modal->status,
                'gateway_name' => 'offline',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (PayfastInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->package->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id,
                'm_payment_id' => $modal->m_payment_id,
                'pf_payment_id' => $modal->pf_payment_id,
                'payfast_plan' => $modal->payfast_plan,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'signature' => $modal->signature,
                'token' => $modal->token,
                'status' => $modal->status,
                'gateway_name' => 'payfast',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (PaypalInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->currency_id,
                'transaction_id' => $modal->transaction_id,
                'sub_total' => $modal->sub_total,
                'amount' => $modal->amount,
                'total' => $modal->total,
                'remarks' => $modal->remarks,
                'billing_frequency' => $modal->billing_frequency,
                'billing_interval' => $modal->billing_interval,
                'pay_date' => $modal->paid_on,
                'next_pay_date' => $modal->next_pay_date,
                'recurring' => $modal->recurring,
                'status' => $modal->status,
                'plan_id' => $modal->plan_id,
                'event_id' => $modal->event_id,
                'end_on' => $modal->end_on,
                'gateway_name' => 'paypal',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (PaystackInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id,
                'transaction_id' => $modal->transaction_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'end_on' => $modal->end_on,
                'gateway_name' => 'paystack',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (RazorpayInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id,
                'transaction_id' => $modal->transaction_id,
                'invoice_id' => $modal->invoice_id,
                'subscription_id' => $modal->subscription_id,
                'order_id' => $modal->order_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'gateway_name' => 'razorpay',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach (StripeInvoice::all() as $modal){
            $invoices[] = [
                'company_id' => $modal->company_id,
                'package_id' => $modal->package_id,
                'currency_id' => $modal->currency_id,
                'global_subscription_id' => GlobalSubscription::where('company_id', $modal->company_id)->first()->id ?? null,
                'transaction_id' => $modal->transaction_id,
                'invoice_id' => $modal->invoice_id,
                'amount' => $modal->amount,
                'pay_date' => $modal->pay_date,
                'next_pay_date' => $modal->next_pay_date,
                'gateway_name' => 'stripe',
                'created_at' => $modal->created_at,
                'updated_at' => $modal->updated_at,
            ];
        }

        foreach ($invoices as $invoice){

            GlobalInvoice::create($invoice);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paystack_subscriptions');
    }

};
