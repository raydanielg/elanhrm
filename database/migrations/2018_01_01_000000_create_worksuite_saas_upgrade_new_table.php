<?php

use App\Models\TranslateSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    /**
     * Run the migrations.
     * We have changed the file_name as 2018 for the purpose of modules
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('employee_shifts')) {
            Schema::create('employee_shifts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('shift_name');
                $table->string('shift_short_code');
                $table->string('color');
                $table->time('office_start_time');
                $table->time('office_end_time');
                $table->time('halfday_mark_time')->nullable();
                $table->tinyInteger('late_mark_duration');
                $table->tinyInteger('clockin_in_day');
                $table->text('office_open_days');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('company_addresses')) {
            Schema::create('company_addresses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->mediumText('address');
                $table->boolean('is_default');
                $table->string('tax_number')->nullable();
                $table->string('tax_name')->nullable();
                $table->string('location')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('client_notes')) {
            Schema::create('client_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('client_id')->nullable()->index('client_notes_client_id_foreign');
                $table->string('title');
                $table->boolean('type')->default(false);
                $table->unsignedInteger('member_id')->nullable()->index('client_notes_member_id_foreign');
                $table->boolean('is_client_show')->default(false);
                $table->boolean('ask_password')->default(false);
                $table->longText('details');
                $table->unsignedInteger('added_by')->nullable()->index('client_notes_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('client_notes_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['client_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['member_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('credit_note_item_images')) {
            Schema::create('credit_note_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('credit_note_item_id')->index('credit_note_item_images_credit_note_item_id_foreign');
                $table->foreign(['credit_note_item_id'])->references(['id'])->on('credit_note_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename')->nullable();
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('database_backup_cron_settings')) {
            Schema::create('database_backup_cron_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->enum('status', ['active', 'inactive'])->default('inactive');
                $table->time('hour_of_day')->nullable();
                $table->string('backup_after_days')->nullable();
                $table->string('delete_backup_after_days')->nullable();
            });
            Schema::create('database_backups', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('filename')->nullable();
                $table->string('size')->nullable();
                $table->dateTime('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('emergency_contacts')) {
            Schema::create('emergency_contacts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id')->index('emergency_contacts_user_id_foreign');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('mobile')->nullable();
                $table->string('relation')->nullable();
                $table->string('address')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('emergency_contacts_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('emergency_contacts_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_shift_schedules')) {
            Schema::create('employee_shift_schedules', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('user_id')->index('employee_shift_schedules_user_id_foreign');
                $table->date('date')->index();
                $table->unsignedBigInteger('employee_shift_id')->index('employee_shift_schedules_employee_shift_id_foreign');
                $table->unsignedInteger('added_by')->nullable()->index('employee_shift_schedules_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('employee_shift_schedules_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['employee_shift_id'])->references(['id'])->on('employee_shifts')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->dateTime('shift_start_time')->nullable();
                $table->dateTime('shift_end_time')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_shift_change_requests')) {
            Schema::create('employee_shift_change_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('shift_schedule_id')->index('employee_shift_change_requests_shift_schedule_id_foreign');
                $table->unsignedBigInteger('employee_shift_id')->index('employee_shift_change_requests_employee_shift_id_foreign');
                $table->foreign(['employee_shift_id'])->references(['id'])->on('employee_shifts')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['shift_schedule_id'])->references(['id'])->on('employee_shift_schedules')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->enum('status', ['waiting', 'accepted', 'rejected'])->default('waiting');
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('estimate_item_images')) {
            Schema::create('estimate_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('estimate_item_id')->index('estimate_item_images_estimate_item_id_foreign');
                $table->foreign(['estimate_item_id'])->references(['id'])->on('estimate_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('file_storage_settings') && !Schema::hasTable('storage_settings')) {
            Schema::create('file_storage_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('filesystem');
                $table->text('auth_keys')->nullable();
                $table->enum('status', ['enabled', 'disabled'])->default('disabled');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('client_id')->nullable()->index('orders_client_id_foreign');
                $table->date('order_date');
                $table->double('sub_total', 8, 2);
                $table->double('discount')->default(0);
                $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
                $table->double('total', 8, 2);
                $table->enum('status', ['pending', 'on-hold', 'failed', 'processing', 'completed', 'canceled', 'refunded'])->default('pending');
                $table->unsignedInteger('currency_id')->nullable()->index('orders_currency_id_foreign');
                $table->enum('show_shipping_address', ['yes', 'no'])->default('no');
                $table->string('note')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('orders_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('orders_last_updated_by_foreign');
                $table->unsignedBigInteger('company_address_id')->nullable()->index('orders_company_address_id_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['client_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['company_address_id'])->references(['id'])->on('company_addresses')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['currency_id'])->references(['id'])->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('invoice_item_images')) {
            Schema::create('invoice_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('invoice_item_id')->index('invoice_item_images_invoice_item_id_foreign');
                $table->foreign(['invoice_item_id'])->references(['id'])->on('invoice_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('invoice_recurring_item_images')) {
            Schema::create('invoice_recurring_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('invoice_recurring_item_id')->index('invoice_recurring_item_images_invoice_recurring_item_id_foreign');
                $table->foreign(['invoice_recurring_item_id'])->references(['id'])->on('invoice_recurring_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->text('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('knowledge_categories')) {
            Schema::create('knowledge_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('knowledge_bases')) {
            Schema::create('knowledge_bases', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('to')->default('employee');
                $table->string('heading')->nullable();
                $table->unsignedInteger('category_id')->nullable()->index('knowledge_bases_category_id_foreign');
                $table->mediumText('description')->nullable();
                $table->unsignedInteger('added_by');
                $table->foreign(['category_id'])->references(['id'])->on('knowledge_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lead_notes')) {
            Schema::create('lead_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('lead_id')->nullable()->index('lead_notes_lead_id_foreign');
                $table->string('title');
                $table->boolean('type')->default(false);
                $table->unsignedInteger('member_id')->nullable()->index('lead_notes_member_id_foreign');
                $table->boolean('is_lead_show')->default(false);
                $table->boolean('ask_password')->default(false);
                $table->string('details');
                $table->unsignedInteger('added_by')->nullable()->index('lead_notes_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('lead_notes_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['lead_id'])->references(['id'])->on('leads')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['member_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lead_user_notes')) {
            Schema::create('lead_user_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index('lead_user_notes_user_id_foreign');
                $table->unsignedInteger('lead_note_id')->index('lead_user_notes_lead_note_id_foreign');
                $table->foreign(['lead_note_id'])->references(['id'])->on('lead_notes')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }


        if (!Schema::hasTable('menu_settings')) {
            Schema::create('menu_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->longText('main_menu')->nullable();
                $table->longText('default_main_menu')->nullable();
                $table->longText('setting_menu')->nullable();
                $table->longText('default_setting_menu')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('menu_name', 100);
                $table->string('translate_name')->nullable();
                $table->string('route', 100)->nullable();
                $table->string('module')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('setting_menu')->nullable();
                $table->timestamps();
            });
        }


        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('order_id')->index('order_items_order_id_foreign');
                $table->unsignedInteger('product_id')->nullable()->index('order_items_product_id_foreign');
                $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('item_name');
                $table->text('item_summary')->nullable();
                $table->enum('type', ['item', 'discount', 'tax'])->default('item');
                $table->double('quantity', 16, 2);
                $table->integer('unit_price');
                $table->double('amount', 8, 2);
                $table->string('hsn_sac_code')->nullable();
                $table->string('taxes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('order_item_images')) {
            Schema::create('order_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('order_item_id')->nullable()->index();
                $table->foreign(['order_item_id'])->references(['id'])->on('order_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permission_types')) {
            Schema::create('permission_types', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_files')) {
            Schema::create('product_files', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('product_id')->index('product_files_product_id_foreign');
                $table->string('filename', 200)->nullable();
                $table->string('hashname', 200)->nullable();
                $table->string('size', 200)->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('product_files_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('product_files_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasTable('project_time_log_breaks')) {
            Schema::create('project_time_log_breaks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('project_time_log_id')->nullable()->index('project_time_log_breaks_project_time_log_id_foreign');
                $table->dateTime('start_time')->index();
                $table->dateTime('end_time')->nullable()->index();
                $table->text('reason');
                $table->string('total_hours')->nullable();
                $table->string('total_minutes')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('project_time_log_breaks_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('project_time_log_breaks_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['project_time_log_id'])->references(['id'])->on('project_time_logs')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('proposal_item_images')) {
            Schema::create('proposal_item_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('proposal_item_id')->index('proposal_item_images_proposal_item_id_foreign');
                $table->foreign(['proposal_item_id'])->references(['id'])->on('proposal_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->mediumText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('ticket_email_settings')) {
            Schema::create('ticket_email_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('mail_username')->nullable();
                $table->string('mail_password')->nullable();
                $table->string('mail_from_name')->nullable();
                $table->string('mail_from_email')->nullable();
                $table->string('imap_host')->nullable();
                $table->string('imap_port')->nullable();
                $table->string('imap_encryption')->nullable();
                $table->boolean('status');
                $table->boolean('verified');
                $table->integer('sync_interval')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('translate_settings')) {
            Schema::create('translate_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('google_key')->nullable();
                $table->timestamps();
            });

            TranslateSetting::create(['google_key' => null]);

        }

        if (!Schema::hasTable('user_invitations')) {
            Schema::create('user_invitations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id')->index('user_invitations_user_id_foreign');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->enum('invitation_type', ['email', 'link'])->default('email');
                $table->string('email')->nullable();
                $table->string('invitation_code');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('email_restriction')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_leadboard_settings')) {
            Schema::create('user_leadboard_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id')->index('user_leadboard_settings_user_id_foreign');
                $table->unsignedInteger('board_column_id')->index('user_leadboard_settings_board_column_id_foreign');
                $table->foreign(['board_column_id'])->references(['id'])->on('lead_status')->onUpdate('CASCADE');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->boolean('collapsed')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('user_id')->index('user_permissions_user_id_foreign');
                $table->unsignedInteger('permission_id')->index('user_permissions_permission_id_foreign');
                $table->unsignedBigInteger('permission_type_id')->index('user_permissions_permission_type_id_foreign');
                $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['permission_type_id'])->references(['id'])->on('permission_types')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_taskboard_settings')) {
            Schema::create('user_taskboard_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id')->index('user_taskboard_settings_user_id_foreign');
                $table->unsignedInteger('board_column_id')->index('user_taskboard_settings_board_column_id_foreign');
                $table->foreign(['board_column_id'])->references(['id'])->on('taskboard_columns')->onUpdate('CASCADE');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->boolean('collapsed')->default(false);
                $table->timestamps();
            });
        }

        Schema::dropIfExists('failed_jobs');
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

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

};
