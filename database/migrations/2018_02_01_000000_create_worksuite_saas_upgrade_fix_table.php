<?php

use App\Models\OfflinePaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     * We have changed the file_name as 2018 for the purpose of modules
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('modules', 'is_superadmin')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->boolean('is_superadmin')->default(0);
            });
        }

        if (!Schema::hasColumn('permissions', 'is_custom')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->boolean('is_custom')->default(false);
                $table->text('allowed_permissions')->nullable();
            });
        }

        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'ModulePermissionSeeder',
            '--force' => true,
        ]);

        if (!Schema::hasColumn('contracts', 'currency_id')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->string('hash')->nullable();
                $table->string('cell')->nullable();
                $table->string('office')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->foreign('last_updated_by')->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->integer('currency_id')->unsigned()->nullable();
                $table->foreign('currency_id')->references('id')
                    ->on('currencies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }

        if (!Schema::hasColumn('attendances', 'location_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unsignedBigInteger('location_id')->nullable()->index('attendances_location_id_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('attendances_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('attendances_last_updated_by_foreign');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->dateTime('shift_start_time')->nullable();
                $table->dateTime('shift_end_time')->nullable();
                $table->unsignedBigInteger('employee_shift_id')->nullable()->index('attendances_employee_shift_id_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['employee_shift_id'])->references(['id'])->on('employee_shifts')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['location_id'])->references(['id'])->on('company_addresses')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('client_contacts', 'title')) {
            Schema::table('client_contacts', function (Blueprint $table) {
                $table->string('title')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('client_contacts_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('client_contacts_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');

            });
        }

        if (Schema::hasColumn('client_details', 'name')) {
            Schema::table('client_details', function (Blueprint $table) {
                $table->dropForeign('client_details_country_id_foreign');
                $table->dropColumn(['name', 'email', 'image', 'mobile', 'office_phone', 'email_notifications', 'country_id']);
            });
        }

        if (!Schema::hasColumn('client_docs', 'added_by')) {
            Schema::table('client_docs', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('client_docs_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('client_docs_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('client_user_notes', 'client_note_id')) {
            Schema::table('client_user_notes', function (Blueprint $table) {
                $table->dropForeign('client_user_notes_note_id_foreign');
                $table->dropColumn('note_id');
                $table->unsignedInteger('client_note_id')->index('client_user_notes_client_note_id_foreign')->after('user_id');
                $table->foreign(['client_note_id'])->references(['id'])->on('client_notes')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (Schema::hasColumn('employee_leave_quotas', 'company_id')) {
            Schema::table('employee_leave_quotas', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (!Schema::hasColumn('attendance_settings', 'allow_shift_change')) {
            Schema::table('attendance_settings', function (Blueprint $table) {
                $table->enum('auto_clock_in', ['yes', 'no'])->default('no');
                $table->boolean('save_current_location')->default(false);
                $table->unsignedBigInteger('default_employee_shift')->nullable()->default(null)->index('attendance_settings_default_employee_shift_foreign');
                $table->foreign(['default_employee_shift'])->references(['id'])->on('employee_shifts')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('week_start_from')->default('1');
                $table->boolean('allow_shift_change')->default(true);
                $table->enum('show_clock_in_button', ['yes', 'no'])->default('no');
            });
        }


        if (!Schema::hasColumn('events', 'remind_type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->integer('remind_time')->nullable();
                $table->enum('remind_type', ['day', 'hour', 'minute'])->default('day');
            });
        }

        if (!Schema::hasColumn('expenses_category', 'added_by')) {
            Schema::table('expenses_category', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('expenses_recurring', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('purchase_from')->nullable()->after('description');
            });

            Schema::table('holidays', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('invoice_recurring', function (Blueprint $table) {
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount')->after('shipping_address');
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('leads', function (Blueprint $table) {
                $table->enum('salutation', ['mr', 'mrs', 'miss', 'dr', 'sir', 'madam'])->nullable();
                $table->string('client_email')->nullable()->change();
                $table->string('cell')->nullable();
                $table->string('office')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('leads_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('leads_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->text('hash')->nullable();
            });

            Schema::table('lead_sources', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('lead_agents', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });


            Schema::table('lead_category', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('lead_custom_forms', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('lead_files', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('lead_follow_up', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('lead_follow_up_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('lead_follow_up_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->enum('send_reminder', ['yes', 'no'])->nullable()->default('no');
                $table->text('remind_time')->nullable();
                $table->enum('remind_type', ['minute', 'hour', 'day'])->nullable();
            });

            Schema::table('leaves', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('project_category', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('project_files', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('project_members', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('project_milestones', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('project_notes', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('project_ratings', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('task_category', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('task_comments', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('task_files', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('task_notes', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');

                $foreignKeys = $this->listTableForeignKeys('task_notes');

                if (in_array('task_notes_company_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['company_id']);
                }

                $table->dropColumn('company_id');
            });

            Schema::table('sub_tasks', function (Blueprint $table) {
                $table->unsignedInteger('assigned_to')->nullable()->index('sub_tasks_assigned_to_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('sub_tasks_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('sub_tasks_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['assigned_to'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('teams', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->unsignedInteger('country_id')->nullable()->index('tickets_country_id_foreign')->after('close_date');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');

                $table->foreign(['country_id'])->references(['id'])->on('countries')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('mobile')->nullable()->after('close_date');

            });

            Schema::table('ticket_agent_groups', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index();
                $table->unsignedInteger('last_updated_by')->nullable()->index();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });

            Schema::table('ticket_custom_forms', function (Blueprint $table) {
                $table->unsignedInteger('custom_fields_id')->nullable()->index('ticket_custom_forms_custom_fields_id_foreign')->after('company_id');
                $table->foreign(['custom_fields_id'])->references(['id'])->on('custom_fields')->onUpdate('CASCADE')->onDelete('CASCADE');
            });

            Schema::table('ticket_files', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('ticket_files');

                if (in_array('ticket_files_company_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['company_id']);
                }

                $table->dropColumn('company_id');
            });

            Schema::table('ticket_replies', function (Blueprint $table) {

                $foreignKeys = $this->listTableForeignKeys('ticket_replies');

                if (in_array('ticket_replies_company_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['company_id']);
                }

                $table->dropColumn('company_id');
                $table->string('imap_message_id')->nullable();
                $table->string('imap_message_uid')->nullable();
                $table->string('imap_in_reply_to')->nullable();
            });

            Schema::table('project_user_notes', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('project_user_notes');

                if (in_array('project_user_notes_company_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['company_id']);
                }
                $table->dropColumn('company_id');
            });

            Schema::table('proposals', function (Blueprint $table) {
                $table->dropColumn('send_status');
            });
            Schema::table('proposal_items', function (Blueprint $table) {
                $table->dropForeign(['tax_id']);
                $table->dropColumn('tax_id');
            });

            Schema::table('proposal_signs', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('proposal_signs');

                if (in_array('proposal_signs_company_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['company_id']);
                }
                $table->dropColumn('company_id');
            });

            Schema::table('purpose_consent', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('pusher_settings', function (Blueprint $table) {
                $table->renameColumn('taskboard_status', 'taskboard');
                $table->renameColumn('message_status', 'messages');
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('sub_task_files', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });

            Schema::table('slack_settings', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('inactive')->after('slack_logo');
            });

            Schema::table('smtp_settings', function (Blueprint $table) {
                $table->enum('mail_connection', ['sync', 'database'])->default('sync');
            });

            Schema::table('users', function (Blueprint $table) {
                \DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` ENUM('male', 'female', 'others') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
                $table->string('email')->nullable()->change();
                $table->dropColumn(['email_verification_code', 'social_token', 'authorize_id', 'authorize_payment_id', 'card_brand', 'card_last_four']);
            });

        }

        if (Schema::hasColumn('payment_gateway_credentials', 'stripe_client_id')) {
            Schema::table('payment_gateway_credentials', function (Blueprint $table) {

                $table->string('test_stripe_client_id')->nullable()->after('stripe_client_id');
                $table->string('test_stripe_secret')->nullable()->after('test_stripe_client_id');
                $table->string('test_stripe_webhook_secret')->nullable()->after('test_stripe_secret');
                $table->enum('stripe_mode', ['test', 'live'])->default('live')->after('test_stripe_webhook_secret');

                $table->renameColumn('stripe_client_id', 'live_stripe_client_id');
                $table->renameColumn('stripe_secret', 'live_stripe_secret');
                $table->renameColumn('stripe_webhook_secret', 'live_stripe_webhook_secret');

                $table->renameColumn('razorpay_key', 'live_razorpay_key');
                $table->renameColumn('razorpay_secret', 'live_razorpay_secret');
                $table->string('test_razorpay_webhook_secret')->nullable()->after('razorpay_secret');


                $table->string('test_razorpay_key')->nullable()->after('test_razorpay_webhook_secret');
                $table->string('test_razorpay_secret')->nullable()->after('test_razorpay_key');
                $table->string('live_razorpay_webhook_secret')->nullable()->after('test_razorpay_secret');
                $table->enum('razorpay_mode', ['test', 'live'])->default('live')->after('live_razorpay_webhook_secret');

                $table->renameColumn('payfast_salt_passphrase', 'payfast_passphrase');
                $table->string('sandbox_paypal_client_id')->nullable();
                $table->string('sandbox_paypal_secret')->nullable();

                $table->string('paystack_key')->nullable();
                $table->string('test_paystack_key')->nullable();
                $table->string('test_paystack_secret')->nullable();
                $table->string('test_paystack_merchant_email')->nullable();
                $table->enum('paystack_mode', ['sandbox', 'live'])->default('live')->after('test_paystack_merchant_email');

                $table->renameColumn('payfast_key', 'payfast_merchant_id')->nullable();
                $table->renameColumn('payfast_secret', 'payfast_merchant_key')->nullable();

                $table->string('square_application_id')->nullable();
                $table->string('square_access_token')->nullable();
                $table->string('square_location_id')->nullable();
                $table->enum('square_environment', ['sandbox', 'production'])->default('sandbox');

                $table->enum('square_status', ['active', 'deactive'])->default('deactive');
                $table->enum('flutterwave_status', ['active', 'deactive'])->default('deactive');
                $table->enum('flutterwave_mode', ['sandbox', 'live'])->default('sandbox');
                $table->string('test_flutterwave_key')->nullable();
                $table->string('test_flutterwave_secret')->nullable();
                $table->string('test_flutterwave_hash')->nullable();
                $table->string('live_flutterwave_key')->nullable();
                $table->string('live_flutterwave_secret')->nullable();
                $table->string('live_flutterwave_hash')->nullable();
                $table->string('flutterwave_webhook_secret_hash')->nullable();


                $table->removeColumn('paystack_client_id');
            });
        }


        if (!Schema::hasColumn('permission_role', 'permission_type_id')) {
            Schema::table('permission_role', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_type_id')->default(5)->index('permission_role_permission_type_id_foreign');
                $table->foreign(['permission_type_id'])->references(['id'])->on('permission_types')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasColumn('permission_role', 'permission_type_id')) {
            Schema::table('permission_role', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_type_id')->default(5)->index('permission_role_permission_type_id_foreign');
                $table->foreign(['permission_type_id'])->references(['id'])->on('permission_types')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasColumn('email_notification_settings', 'slug')) {
            Schema::table('email_notification_settings', function (Blueprint $table) {
                $table->string('slug')->nullable();
            });
        }


        if (!Schema::hasColumn('theme_settings', 'sidebar_theme')) {
            Schema::table('theme_settings', function (Blueprint $table) {
                $table->enum('sidebar_theme', ['dark', 'light'])->default('dark');
            });
        }

        if (!Schema::hasColumn('employee_details', 'date_of_birth')) {
            Schema::table('employee_details', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('employee_details_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('employee_details_last_updated_by_foreign');
                $table->date('date_of_birth')->nullable();
                $table->text('calendar_view')->nullable();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }


        if (Schema::hasTable('storage_settings')) {
            Schema::dropIfExists('file_storage_settings');
            Schema::rename('storage_settings', 'file_storage_settings');
        }

        if (!Schema::hasColumn('file_storage', 'filename')) {
            Schema::table('file_storage', function (Blueprint $table) {
                $table->renameColumn('name', 'filename');
            });

        }


        if (Schema::hasTable('google_accounts')) {
            Schema::dropIfExists('google_accounts');
        }

        if (Schema::hasColumn('google_calendar_modules', 'user_id')) {
            Schema::table('google_calendar_modules', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }


        if (!Schema::hasColumn('projects', 'hash')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedInteger('team_id')->nullable()->index('projects_team_id_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('projects_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('projects_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['team_id'])->references(['id'])->on('teams')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->text('hash')->nullable();
                $table->boolean('public');
                $table->dropColumn(['read_only', 'visible_rating_employee']);
            });
            Schema::table('project_activity', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (!Schema::hasColumn('tasks', 'added_by')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('tasks_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('tasks_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->boolean('repeat')->default(false);
                $table->boolean('repeat_complete')->default(false);
                $table->integer('repeat_count')->nullable();
                $table->enum('repeat_type', ['day', 'week', 'month', 'year'])->default('day');
                $table->integer('repeat_cycles')->nullable();
                $table->removeColumn('task_request_id');
            });
        }

        if (!Schema::hasColumn('project_time_logs', 'added_by')) {
            Schema::table('project_time_logs', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('project_time_logs_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('project_time_logs_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('total_break_minutes')->nullable();
            });
        }

        if (!Schema::hasColumn('client_details', 'added_by')) {
            Schema::table('client_details', function (Blueprint $table) {
                $table->string('office')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('client_details_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('client_details_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('proposals', 'hash')) {
            Schema::table('proposals', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('proposals_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('proposals_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->text('hash')->nullable();
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
            });
        }

        if (!Schema::hasColumn('estimates', 'added_by')) {
            Schema::table('estimates', function (Blueprint $table) {
                $table->longText('description')->nullable()->after('note');
                $table->string('estimate_number')->nullable()->change()->after('client_id');
                $table->unsignedInteger('added_by')->nullable()->index('estimates_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('estimates_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->text('hash')->nullable();
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
                $table->removeColumn('deleted_at');
            });
        }

        if (!Schema::hasColumn('invoices', 'due_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->removeColumn('deleted_at');
                $table->unsignedBigInteger('order_id')->nullable()->index('invoices_order_id_foreign');

                $table->string('invoice_number')->change();
                $table->double('due_amount', 8, 2)->default(0);
                $table->unsignedInteger('parent_id')->nullable()->index('invoices_parent_id_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('invoices_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('invoices_last_updated_by_foreign');
                $table->text('hash')->nullable();
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
                $table->unsignedBigInteger('company_address_id')->nullable()->index('invoices_company_address_id_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['company_address_id'])->references(['id'])->on('company_addresses')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['parent_id'])->references(['id'])->on('invoices')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasColumn('payments', 'order_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->nullable()->index('payments_order_id_foreign');
                $table->unsignedInteger('credit_notes_id')->nullable()->index('payments_credit_notes_id_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('payments_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('payments_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['credit_notes_id'])->references(['id'])->on('credit_notes')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->text('payment_gateway_response')->nullable()->comment('null = success');
                $table->string('payload_id')->nullable();
            });
        }

        if (!Schema::hasColumn('expenses', 'added_by')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('expenses_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('expenses_last_updated_by_foreign');
                $table->unsignedInteger('approver_id')->nullable()->index('expenses_approver_id_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['approver_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('products', 'added_by')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('downloadable')->default(false);
                $table->string('downloadable_file')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('products_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('products_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('default_image')->nullable();
            });
        }

        if (!Schema::hasColumn('notices', 'added_by')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('notices_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('notices_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('credit_notes', 'adjustment_amount')) {
            Schema::table('credit_notes', function (Blueprint $table) {
                $table->double('adjustment_amount', 8, 2)->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('credit_notes_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('credit_notes_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
            });
        }


        if (!Schema::hasColumn('companies', 'light_logo')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->text('token')->nullable();
                $table->string('logo_background_color')->nullable();
                $table->string('light_logo')->nullable();
                $table->string('favicon')->nullable();
                $table->enum('auth_theme', ['dark', 'light'])->default('light');
                $table->enum('sidebar_logo_style', ['square', 'full'])->default('square');
                $table->integer('before_days');
                $table->integer('after_days');
                $table->enum('on_deadline', ['yes', 'no'])->default('yes');
                $table->integer('taskboard_length')->default(10);
                $table->boolean('allow_client_signup');
                $table->boolean('admin_client_signup_approval');
                $table->enum('google_calendar_status', ['active', 'inactive'])->default('inactive');
                $table->text('google_client_id')->nullable();
                $table->text('google_client_secret')->nullable();
                $table->enum('google_calendar_verification_status', ['verified', 'non_verified'])->default('non_verified');
                $table->string('name')->nullable();
            });
        }

        if (!Schema::hasColumn('invoice_settings', 'reminder')) {
            Schema::table('invoice_settings', function (Blueprint $table) {
                $table->enum('reminder', ['after', 'every'])->nullable();
                $table->integer('send_reminder_after')->default(0);
                $table->boolean('tax_calculation_msg')->default(false);
                $table->integer('show_project')->default(0);
                $table->enum('show_client_name', ['yes', 'no'])->nullable()->default('no');
                $table->enum('show_client_email', ['yes', 'no'])->nullable()->default('no');
                $table->enum('show_client_phone', ['yes', 'no'])->nullable()->default('no');
                $table->enum('show_client_company_address', ['yes', 'no'])->nullable()->default('no');
                $table->enum('show_client_company_name', ['yes', 'no'])->nullable()->default('no');
            });
        }

        if (!Schema::hasColumn('task_label_list', 'project_id')) {
            Schema::table('task_label_list', function (Blueprint $table) {
                $table->unsignedInteger('project_id')->nullable()->index('task_label_list_project_id_foreign');
                $table->foreign(['project_id'])->references(['id'])->on('projects')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasColumn('employee_docs', 'last_updated_by')) {
            Schema::table('employee_docs', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('employee_docs_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('employee_docs_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('lead_custom_forms', 'custom_fields_id')) {
            Schema::table('lead_custom_forms', function (Blueprint $table) {
                $table->unsignedInteger('custom_fields_id')->nullable()->index('lead_custom_forms_custom_fields_id_foreign')->after('company_id');
                $table->foreign(['custom_fields_id'])->references(['id'])->on('custom_fields')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (Schema::hasColumn('companies', 'licence_expire_on') && !Schema::hasColumn('companies', 'subscription_updated_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->timestamp('license_updated_at')->nullable()->after('licence_expire_on');
                $table->timestamp('subscription_updated_at')->nullable()->after('license_updated_at');
            });
        }

        Schema::table('client_details', function (Blueprint $table) {
            $table->string('mobile')->nullable();
            $table->string('office_phone')->nullable();
            $table->unsignedInteger('country_id')->nullable()->index('client_details_country_id_foreign');
            $table->foreign(['country_id'])->references(['id'])->on('countries')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        $this->changePackageId();

        if (Schema::hasTable('offline_payment_methods')) {
            OfflinePaymentMethod::all()->each(function ($offlinePaymentMethod) {
                $offlinePaymentMethod->update([
                    'description' => strip_tags($offlinePaymentMethod->description)
                ]);
            });
        }

        if (Schema::hasTable('stripe_invoices')) {
            if (!Schema::hasColumn('stripe_invoices', 'invoice_number')) {
                Schema::table('stripe_invoices', function (Blueprint $table) {
                    $table->string('stripe_invoice_number')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('module_settings', 'is_allowed')) {
            Schema::table('module_settings', function (Blueprint $table) {
                $table->boolean('is_allowed')->default(1);
            });
        }

        if (!Schema::hasColumn('slack_settings', 'status')) {
            Schema::table('slack_settings', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('inactive');
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
        // Removed all drop code to minimize the file size.
    }

    private function changePackageId()
    {
        if (Schema::hasTable('packages')) {
            $tables = [
                'companies' => 'package_id',
                'offline_invoices' => 'package_id',
                'offline_plan_changes' => 'package_id',
                'stripe_invoices' => 'package_id',
                'razorpay_invoices' => 'package_id',
                'paystack_invoices' => 'package_id',
                'authorize_invoices' => 'package_id',
                'mollie_invoices' => 'package_id',
                'paypal_invoices' => 'package_id',
                'payfast_invoices' => 'package_id',
                'licences' => 'package_id',
                'authorize_subscriptions' => 'plan_id',
            ];

            // Drop Foreign Key
            foreach ($tables as $tableName => $foreignKey) {
                if (Schema::hasTable($tableName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign([$foreignKey]);
                    });
                }
            }

            // Change package id to bigInteger
            Schema::table('packages', function (Blueprint $table) {
                $table->id()->change();
            });

            // Create Foreign Key
            foreach ($tables as $tableName => $foreignKey) {
                if (Schema::hasTable($tableName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
                        $table->unsignedBigInteger($foreignKey)->nullable()->change();
                        $table->foreign([$foreignKey])->references(['id'])->on('packages')->onUpdate('CASCADE')->onDelete('SET NULL');
                    });
                }
            }
        }
    }

    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }

};
