<?php

use Illuminate\Support\Facades\Schema;
use App\Models\SuperAdmin\SupportTicket;
use App\Models\SuperAdmin\GlobalCurrency;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('global_currencies')) {
            Schema::create('global_currencies', function (Blueprint $table) {
                $table->id();
                $table->string('currency_name');
                $table->string('currency_symbol');
                $table->string('currency_code');
                $table->double('exchange_rate')->nullable()->default(null);
                $table->double('usd_price')->nullable()->default(null);
                $table->enum('is_cryptocurrency', ['yes', 'no'])->default('no');
                $table->enum('currency_position', ['front', 'behind'])->default('front');
                $table->enum('status', ['enable', 'disable'])->default('enable');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('global_settings', 'company_email')) {
            Schema::table('global_settings', function (Blueprint $table) {
                $table->string('company_email');
                $table->string('company_phone')->nullable();
                $table->text('address')->nullable();
                $table->string('website')->nullable();
                $table->unsignedBigInteger('currency_id')->nullable()->default(null);
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('global_currencies')
                    ->onDelete(null)
                    ->onUpdate('cascade');
                if(!Schema::hasColumns('global_settings', ['date_format', 'time_format', 'google_map_key'])){
                    $table->string('date_format', 20)->default('d-m-Y');
                    $table->string('time_format', 20)->default('h:i a');
                    $table->string('google_map_key');
                }
                $table->string('date_picker_format')->nullable();
                $table->decimal('latitude', 10, 8)->default('26.9124336');
                $table->decimal('longitude', 11, 8)->default('75.78727090000007');
                $table->enum('active_theme', ['default', 'custom'])->default('default');
                $table->integer('last_updated_by')->unsigned()->nullable();
                $table->foreign('last_updated_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                $table->boolean('rounded_theme');
                $table->boolean('front_design')->default(1);
                $table->boolean('email_verification')->default(0);
                $table->string('logo_front')->nullable();
                $table->boolean('login_ui');
                $table->longText('auth_css')->nullable()->default(null);
                $table->longText('auth_css_theme_two')->nullable()->default(null);
                $table->string('new_company_locale')->nullable()->default(null);
                $table->boolean('frontend_disable')->default(false);
                $table->string('setup_homepage')->default('default');
                $table->string('custom_homepage_url')->nullable();
                $table->text('expired_message')->nullable();
                $table->boolean('enable_register')->default(1);
                $table->boolean('registration_open')->default(1);
            });
        }

        if (!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('currency_id')->nullable()->default(null);
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('global_currencies')
                    ->onDelete(null)
                    ->onUpdate('cascade');
                $table->string('name', 255);
                $table->string('description', 1000)->nullable();
                $table->integer('max_storage_size');
                $table->unsignedInteger('max_file_size')->default(0);
                $table->decimal('annual_price')->default(0);
                $table->decimal('monthly_price')->default(0);
                $table->unsignedTinyInteger('billing_cycle')->default(0);
                $table->integer('max_employees')->unsigned()->default(0);
                $table->string('sort');
                $table->string('module_in_package', 1000);
                $table->string('stripe_annual_plan_id', 255)->nullable();
                $table->string('stripe_monthly_plan_id', 255)->nullable();
                $table->string('razorpay_annual_plan_id')->nullable()->default(null);
                $table->string('razorpay_monthly_plan_id')->nullable()->default(null);
                $table->enum('default', ['yes', 'no', 'trial'])->nullable()->default('no');
                $table->string('paystack_monthly_plan_id')->nullable();
                $table->string('paystack_annual_plan_id')->nullable();
                $table->boolean('is_private');
                $table->enum('storage_unit', ['gb', 'mb'])->default('mb');
                $table->boolean('is_recommended')->default(0);
                $table->boolean('is_free')->default(0);
                $table->boolean('is_auto_renew')->default(0);
                $table->string('monthly_status')->nullable()->default(1);
                $table->string('annual_status')->nullable()->default(1);
                $table->timestamps();
            });

            Schema::table('companies', function (Blueprint $table) {
                $table->unsignedBigInteger('package_id')->nullable()->after('currency_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                $table->enum('package_type', ['monthly', 'annual'])
                    ->after('package_id')
                    ->default('monthly');
                $table->string('stripe_id')->nullable();
                $table->string('card_brand')->nullable();
                $table->string('card_last_four')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->date('licence_expire_on')->nullable();
                $table->timestamp('license_updated_at')->nullable();
                $table->timestamp('subscription_updated_at')->nullable();
            });

        }

        if (!Schema::hasTable('package_settings')) {
            Schema::create('package_settings', function (Blueprint $table) {
                $table->id();
                $table->enum('status', ['active', 'inactive'])->default('inactive');
                $table->integer('no_of_days')->nullable()->default(30);
                $table->string('modules', 1000)->nullable()->default(null);
                $table->text('trial_message')->nullable();
                $table->integer('notification_before')->nullable()->default(null);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('offline_invoices')) {
            Schema::create('offline_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('package_type')->nullable();
                $table->integer('offline_method_id')->unsigned()->nullable();
                $table->foreign('offline_method_id')
                    ->references('id')
                    ->on('offline_payment_methods')
                    ->onDelete('SET NULL')
                    ->onUpdate('cascade');
                $table->string('transaction_id')->nullable();
                $table->unsignedDecimal('amount', 12, 2);
                $table->date('pay_date');
                $table->date('next_pay_date')->nullable();
                $table->enum('status', ['paid', 'unpaid', 'pending'])->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('offline_plan_changes')) {
            Schema::create('offline_plan_changes', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('package_type');
                $table->double('amount')->nullable();
                $table->date('pay_date')->nullable();
                $table->date('next_pay_date')->nullable();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('offline_invoices')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedInteger('offline_method_id');
                $table->foreign('offline_method_id')
                    ->references('id')
                    ->on('offline_payment_methods')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('file_name')->nullable();
                $table->enum('status', ['verified', 'pending', 'rejected'])->default('pending');
                $table->text('remark')->nullable();
                $table->mediumText('description');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stripe_invoices')) {
            Schema::create('stripe_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('invoice_id')->nullable();
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('transaction_id')->nullable();
                $table->unsignedDecimal('amount', 12, 2);
                $table->date('pay_date');
                $table->date('next_pay_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('razorpay_invoices')) {
            Schema::create('razorpay_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->integer('currency_id')->nullable();
                $table->string('invoice_id');
                $table->string('subscription_id');
                $table->string('order_id')->nullable();
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('transaction_id');
                $table->unsignedDecimal('amount', 12, 2);
                $table->date('pay_date');
                $table->date('next_pay_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paystack_invoices')) {
            Schema::create('paystack_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('transaction_id')->nullable();
                $table->string('amount')->nullable();
                $table->date('pay_date')->nullable();
                $table->date('next_pay_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('authorize_invoices')) {
            Schema::create('authorize_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('transaction_id')->nullable();
                $table->string('amount')->nullable();
                $table->date('pay_date')->nullable();
                $table->date('next_pay_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mollie_invoices')) {
            Schema::create('mollie_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('transaction_id')->nullable();
                $table->string('amount')->nullable();
                $table->string('package_type')->nullable();
                $table->date('pay_date')->nullable();
                $table->date('next_pay_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paypal_invoices')) {
            Schema::create('paypal_invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->integer('currency_id')->unsigned()->nullable();
                $table->foreign('currency_id')
                    ->references('id')
                    ->on('currencies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
                $table->double('sub_total', [15, 2])->nullable()->default(null);
                $table->double('total', [15, 2])->nullable()->default(null);
                $table->string('transaction_id')->nullable()->default(null);
                $table->string('remarks')->nullable()->default(null);
                $table->string('billing_frequency')->nullable()->default(null);
                $table->integer('billing_interval')->nullable()->default(null);
                $table->dateTime('paid_on')->nullable()->default(null);
                $table->dateTime('next_pay_date')->nullable()->default(null);
                $table->enum('recurring', ['yes', 'no'])->nullable()->default('no');
                $table->enum('status', ['paid', 'unpaid', 'pending'])->nullable()->default('pending');
                $table->string('plan_id')->nullable()->default(null);
                $table->string('event_id')->nullable()->default(null);
                $table->dateTime('end_on')->nullable()->default(null);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payfast_invoices')) {
            Schema::create('payfast_invoices', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('m_payment_id')->nullable();
                $table->string('pf_payment_id')->nullable();
                $table->string('payfast_plan')->nullable();
                $table->string('amount')->nullable();
                $table->date('pay_date')->nullable();
                $table->date('next_pay_date')->nullable();
                $table->string('signature')->nullable();
                $table->string('token')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stripe_setting')) {
            Schema::create('stripe_setting', function (Blueprint $table) {
                $table->increments('id');
                $table->string('api_key')->nullable()->default(null);
                $table->string('api_secret')->nullable()->default(null);
                $table->string('webhook_key')->nullable()->default(null);
                $table->string('paypal_client_id')->nullable()->default(null);
                $table->string('paypal_secret')->nullable()->default(null);
                $table->enum('paypal_status', ['active', 'inactive'])->default('inactive');
                $table->enum('stripe_status', ['active', 'inactive'])->default('inactive');
                $table->string('razorpay_key')->nullable()->default(null);
                $table->string('razorpay_secret')->nullable()->default(null);
                $table->string('razorpay_webhook_secret')->nullable()->default(null);
                $table->enum('razorpay_status', ['active', 'deactive'])->default('deactive');
                $table->enum('paypal_mode', ['sandbox', 'live']);
                $table->string('paystack_client_id')->nullable();
                $table->string('paystack_secret')->nullable();
                $table->enum('paystack_status', ['active', 'inactive'])->default('inactive')->nullable();
                $table->string('paystack_merchant_email')->nullable();
                $table->string('paystack_payment_url')->default('https://api.paystack.co')->nullable();
                $table->string('mollie_api_key');
                $table->enum('mollie_status', ['active', 'inactive'])->default('inactive');
                $table->string('authorize_api_login_id')->nullable();
                $table->string('authorize_transaction_key')->nullable();
                $table->string('authorize_signature_key')->nullable();
                $table->string('authorize_environment')->nullable();
                $table->enum('authorize_status', ['active', 'inactive'])->default('inactive');
                $table->string('payfast_key')->nullable();
                $table->string('payfast_secret')->nullable();
                $table->enum('payfast_status', ['active', 'inactive'])->default('inactive');
                $table->string('payfast_salt_passphrase')->nullable();
                $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('faq_categories')) {
            Schema::create('faq_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('image')->nullable()->default(null);
                $table->unsignedBigInteger('faq_category_id');
                $table->foreign('faq_category_id')
                    ->references('id')
                    ->on('faq_categories')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('faq_files')) {
            Schema::create('faq_files', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedBigInteger('faq_id');
                $table->foreign('faq_id')
                    ->references('id')
                    ->on('faqs')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('filename');
                $table->text('description')->nullable();
                $table->string('google_url')->nullable();
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('dropbox_link')->nullable();
                $table->string('external_link')->nullable();
                $table->string('external_link_name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_ticket_types')) {
            Schema::create('support_ticket_types', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->unsignedInteger('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->integer('created_by')->unsigned();
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->text('subject');
                $table->longText('description');
                $table->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->integer('agent_id')->unsigned()->nullable();
                $table->foreign('agent_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                $table->bigInteger('support_ticket_type_id')->unsigned()->nullable();
                $table->foreign('support_ticket_type_id')
                    ->references('id')
                    ->on('support_ticket_types')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_ticket_replies')) {
            Schema::create('support_ticket_replies', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('support_ticket_id')->unsigned();
                $table->foreign('support_ticket_id')
                    ->references('id')
                    ->on('support_tickets')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->longText('message');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_ticket_files')) {
            Schema::create('support_ticket_files', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->bigInteger('support_ticket_reply_id')->unsigned();
                $table->foreign('support_ticket_reply_id')
                    ->references('id')
                    ->on('support_ticket_replies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('filename');
                $table->text('description')->nullable();
                $table->string('google_url')->nullable();
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('dropbox_link')->nullable();
                $table->string('external_link')->nullable();
                $table->string('external_link_name')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('theme_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('theme_settings', 'enable_rounded_theme')) {
                $table->boolean('enable_rounded_theme')->default(0);
            }

            if (!Schema::hasColumn('theme_settings', 'login_background')) {
                $table->string('login_background')->nullable()->default(null);
            }
        });

        if (!Schema::hasTable('front_details')) {
            Schema::create('front_details', function (Blueprint $table) {
                $table->increments('id');
                $table->enum('get_started_show', ['yes', 'no'])->default('yes');
                $table->enum('sign_in_show', ['yes', 'no'])->default('yes');
                $table->text('address')->nullable()->default(null);
                $table->string('phone', 20)->nullable()->default(null);
                $table->string('email', 60)->nullable()->default(null);
                $table->text('social_links')->nullable();
                $table->string('primary_color')->nullable()->default(null);
                $table->longText('custom_css')->nullable()->default(null);
                $table->longText('custom_css_theme_two')->nullable()->default(null);
                $table->string('locale')->nullable()->default('en');
                $table->longText('contact_html')->nullable()->default(null);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tr_front_details')) {
            Schema::create('tr_front_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('header_title', 200);
                $table->text('header_description');
                $table->string('image', 200);
                $table->string('feature_title')->nullable();
                $table->string('feature_description')->nullable();
                $table->string('price_title')->nullable();
                $table->string('price_description')->nullable();
                $table->string('task_management_title')->nullable();
                $table->text('task_management_detail')->nullable();
                $table->string('manage_bills_title')->nullable();
                $table->text('manage_bills_detail')->nullable();
                $table->string('teamates_title')->nullable();
                $table->text('teamates_detail')->nullable();
                $table->string('favourite_apps_title')->nullable();
                $table->text('favourite_apps_detail')->nullable();
                $table->string('cta_title')->nullable();
                $table->text('cta_detail')->nullable();
                $table->string('client_title')->nullable();
                $table->text('client_detail')->nullable();
                $table->string('testimonial_title')->nullable();
                $table->text('testimonial_detail')->nullable();
                $table->string('faq_title')->nullable();
                $table->text('faq_detail')->nullable();
                $table->text('footer_copyright_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('front_features')) {
            Schema::create('front_features', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('title')->nullable()->default(null);
                $table->string('description')->nullable()->default(null);
                $table->enum('status', ['enable', 'disable'])->default('enable');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('title');
                $table->longText('description')->nullable()->default(null);
                $table->string('image', 200)->nullable()->default(null);
                $table->string('icon', 200)->nullable()->default(null);
                $table->enum('type', ['image', 'icon', 'task', 'bills', 'team', 'apps'])->default('image');
                $table->unsignedBigInteger('front_feature_id')->nullable()->default(null);
                $table->foreign('front_feature_id')
                    ->references('id')
                    ->on('front_features')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('footer_menu')) {
            Schema::create('footer_menu', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->longText('description')->nullable()->default(null);
                $table->string('video_link')->nullable()->default(null);
                $table->text('video_embed')->nullable()->default(null);
                $table->string('file_name')->nullable()->default(null);
                $table->string('hash_name')->nullable()->default(null);
                $table->string('external_link')->nullable()->default(null);
                $table->enum('type', ['header', 'footer', 'both'])->nullable()->default('footer');
                $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seo_details')) {
            Schema::create('seo_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('page_name');
                $table->string('seo_title')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->string('seo_description')->nullable();
                $table->string('seo_author')->nullable();
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('og_image')->nullable()->default(null);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('front_clients')) {
            Schema::create('front_clients', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable()->default(null);
                $table->string('image')->nullable()->default(null);
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->Text('comment')->nullable()->default(null);
                $table->float('rating')->nullable()->default(null);
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('front_faqs')) {
            Schema::create('front_faqs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('question');
                $table->text('answer');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('front_widgets')) {
            Schema::create('front_widgets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('widget_code');
                $table->timestamps();
            });
        }


        if (!Schema::hasColumn('front_widgets', 'header_script')) {
            Schema::table('front_widgets', function (Blueprint $table) {
                $table->longtext('header_script')->nullable();
                DB::statement('ALTER TABLE `front_widgets` CHANGE `widget_code` `footer_script` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
            });
        }

        if (!Schema::hasColumn('footer_menu', 'private')) {
            Schema::table('footer_menu', function (Blueprint $table) {
                $table->boolean('private')->default(0);
            });
        }

        if (!Schema::hasTable('sign_up_settings')) {
            Schema::create('sign_up_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_phone')->nullable()->default(null)->change();
        });

        if (!Schema::hasTable('front_menu_buttons')) {
            Schema::create('front_menu_buttons', function (Blueprint $table) {
                $table->increments('id');
                $table->string('home', 20)->nullable()->default('home');
                $table->string('feature', 20)->nullable()->default('feature');
                $table->string('price', 20)->nullable()->default('price');
                $table->string('contact', 20)->nullable()->default('contact');
                $table->string('get_start', 20)->nullable()->default('get_start');
                $table->string('login', 20)->nullable()->default('login');
                $table->string('contact_submit', 20)->nullable()->default('contact_submit');
                $table->unsignedInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')->references('id')->on('language_settings')->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
            });
        }

        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('company_need_approval')->default(0);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('approved')->default(1);

            $table->integer('approved_by')->unsigned()->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
        });

        DB::statement("ALTER TABLE global_currencies CHANGE COLUMN currency_position currency_position ENUM('left', 'right', 'left_with_space', 'right_with_space') NOT NULL DEFAULT 'left'");

        Schema::table('global_currencies', function (Blueprint $table) {
            $table->unsignedInteger('no_of_decimal')->default(2)->after('currency_position');
            $table->string('thousand_separator')->nullable()->after('no_of_decimal');
            $table->string('decimal_separator')->nullable()->after('thousand_separator');
        });

        $currencies = GlobalCurrency::all();

        foreach ($currencies as $currency) {
            $currency->currency_position = 'left';
            $currency->no_of_decimal = 2;
            $currency->thousand_separator = ',';
            $currency->decimal_separator = '.';
            $currency->save();
        }

        if (Schema::hasColumn('users', 'super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('super_admin', 'is_superadmin');
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->boolean('two_factor_confirmed')->default(false);
                $table->boolean('two_factor_email_confirmed')->default(false);
                $table->enum('salutation', ['mr', 'mrs', 'miss', 'dr', 'sir', 'madam'])->nullable();
                $table->boolean('dark_theme');
                $table->boolean('rtl');
                $table->enum('two_fa_verify_via', ['email', 'google_authenticator', 'both'])->nullable();
                $table->string('two_factor_code')->nullable()->comment('when authenticator is email');
                $table->dateTime('two_factor_expires_at')->nullable();
                $table->boolean('admin_approval')->default(true);
                $table->boolean('permission_sync')->default(true);
                $table->boolean('google_calendar_status')->default(true);
            });
        }


        if (!Schema::hasTable('razorpay_subscriptions')) {
            Schema::create('razorpay_subscriptions', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('company_id');
                $table->string('subscription_id')->nullable()->default(null);
                $table->string('customer_id')->nullable()->default(null);
                $table->string('name');
                $table->string('razorpay_id');
                $table->string('razorpay_plan');
                $table->integer('quantity');
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('companies', 'favicon')) {
            Schema::table('companies', function ($table) {
                $table->string('favicon')->nullable()->after('logo');
            });
        }

        if (!Schema::hasColumn('support_tickets', 'company_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->unsignedInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->unsignedInteger('user_id')->nullable()->change();
            });

            $tickets = SupportTicket::with('requester')->get();

            foreach ($tickets as $ticket) {
                $ticket->company_id = $ticket->requester->company_id;
                $ticket->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_currencies');
    }

};
