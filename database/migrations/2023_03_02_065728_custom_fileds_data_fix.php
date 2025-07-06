<?php

use App\Models\Company;
use App\Models\Module;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        DB::table('custom_fields_data')
            ->where('model', 'NOT LIKE', '%Models%')
            ->update(['model' => DB::raw("REPLACE(model, 'App\\\\', 'App\\\\Models\\\\')")]);


        Invoice::with('project')->whereNull('client_id')->get()->each(function ($invoice) {
            $invoice->client_id = $invoice->project->client_id;
            $invoice->saveQuietly();
        });

        Payment::with('company')->get()->each(function ($payment) {
            $payment->currency_id = $payment->company->currency_id;
            $payment->saveQuietly();
        });

        Project::with('company')->get()->each(function ($project) {
            $project->currency_id = $project->company->currency_id;
            $project->saveQuietly();
        });

        if (Schema::hasTable('events')) {
            if (!Schema::hasColumn('events', 'send_reminder')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->enum('send_reminder', ['yes', 'no'])->default('no');
                });
            }

            if (!Schema::hasColumn('events', 'remind_time')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->integer('remind_time')->nullable();
                });
            }

            if (!Schema::hasColumn('events', 'remind_type')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->enum('remind_type', ['day', 'hour', 'minute'])->default('day');
                });
            }

            if (!Schema::hasColumn('events', 'added_by')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->unsignedInteger('added_by')->nullable()->index('events_added_by_foreign');
                    $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
            }

            if (!Schema::hasColumn('events', 'last_updated_by')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->unsignedInteger('last_updated_by')->nullable()->index('events_last_updated_by_foreign');
                    $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
            }
        }

        if (Schema::hasTable('tickets')) {
            if (!Schema::hasColumn('tickets', 'mobile')) {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->string('mobile')->nullable();
                });
            }

            if (!Schema::hasColumn('tickets', 'country_id')) {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->unsignedInteger('country_id')->nullable()->index('tickets_country_id_foreign');
                    $table->foreign(['country_id'])->references(['id'])->on('countries')->onUpdate('CASCADE')->onDelete('CASCADE');
                });
            }

            if (!Schema::hasColumn('tickets', 'added_by')) {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->unsignedInteger('added_by')->nullable()->index('tickets_added_by_foreign');
                    $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
            }

            if (!Schema::hasColumn('tickets', 'last_updated_by')) {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->unsignedInteger('last_updated_by')->nullable()->index('tickets_last_updated_by_foreign');
                    $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
            }
        }

        if (Schema::hasColumn('global_settings', 'google_recaptcha_status')) {
            DB::statement("ALTER TABLE `global_settings` CHANGE `google_recaptcha_status` `google_recaptcha_status` ENUM('active','deactive') NOT NULL DEFAULT 'deactive';");
            GlobalSetting::where('google_recaptcha_status', '')->update(['google_recaptcha_status' => 'deactive']);
        }

        Module::firstOrCreate(['module_name' => 'messages']);

        if (Schema::hasTable('contract_files')) {
            if (!Schema::hasColumn('contract_files', 'added_by')) {
                Schema::table('contract_files', function (Blueprint $table) {
                    $table->unsignedInteger('added_by')->nullable()->index('contract_files_added_by_foreign');
                    $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
            }

            if (!Schema::hasColumn('contract_files', 'last_updated_by')) {
                Schema::table('contract_files', function (Blueprint $table) {
                    $table->unsignedInteger('last_updated_by')->nullable()->index('contract_files_last_updated_by_foreign');
                    $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                });
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
        //
    }

};
