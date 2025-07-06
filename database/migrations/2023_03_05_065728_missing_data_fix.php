<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\Product;
use App\Models\EstimateTemplate;
use App\Models\LanguageSetting;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\ProposalTemplate;
use App\Models\RecurringInvoice;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\UnitType;
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

        // This went with release 5.2.73
        if (Schema::hasColumn('project_notes', 'notes_title')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->renameColumn('notes_title', 'title');
                $table->renameColumn('notes_type', 'type');

            });
        }

        if (Schema::hasColumn('project_notes', 'note_details')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->renameColumn('note_details', 'details');
            });
        }


        if (Schema::hasColumn('project_user_notes', 'project_notes_id')) {
            Schema::table('project_user_notes', function (Blueprint $table) {
                $table->renameColumn('project_notes_id', 'project_note_id');
            });
        }

        if (!Schema::hasTable('lead_products')) {
            Schema::create('lead_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('lead_id')->index('lead_products_lead_id_foreign');
                $table->unsignedInteger('product_id')->index('lead_products_product_id_foreign');
                $table->foreign(['lead_id'])->references(['id'])->on('leads')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('contracts', 'added_by')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('contracts_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('project_milestones', 'start_date')) {
            Schema::table('project_milestones', function (Blueprint $table) {
                $table->date('start_date')->nullable();
                $table->renameColumn('due_date', 'end_date');
            });
        }

        if (Schema::hasColumn('project_milestones', 'due_date')) {
            Schema::table('project_milestones', function (Blueprint $table) {
                $table->renameColumn('due_date', 'end_date');
            });
        }

        if (Schema::hasColumn('project_notes', 'note_details')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->renameColumn('note_details', 'details');
            });
        }

        if (Schema::hasColumn('project_notes', 'details')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->longText('details')->change();
            });
        }

        if (Schema::hasColumn('project_notes', 'note_title')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->renameColumn('note_title', 'title');
            });
        }

        if (Schema::hasColumn('project_notes', 'note_type')) {
            Schema::table('project_notes', function (Blueprint $table) {
                $table->renameColumn('note_type', 'type');
            });
        }

        if (!Schema::hasColumn('subscriptions', 'user_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->nullable()->after('company_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
            });
        }

        if (!Schema::hasColumn('credit_note_items', 'item_summary')) {
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->text('item_summary')->nullable()->after('hsn_sac_code');
            });
        }

        if (!Schema::hasColumn('contract_discussions', 'added_by')) {
            Schema::table('contract_discussions', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('contract_discussions_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('contract_discussions', 'last_updated_by')) {
            Schema::table('contract_discussions', function (Blueprint $table) {
                $table->unsignedInteger('last_updated_by')->nullable()->index('contract_discussions_last_updated_by_foreign');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('contract_renews', 'added_by')) {
            Schema::table('contract_renews', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('contract_renews_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('contract_renews', 'last_updated_by')) {
            Schema::table('contract_renews', function (Blueprint $table) {
                $table->unsignedInteger('last_updated_by')->nullable()->index('contract_renews_last_updated_by_foreign');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('designations', 'added_by')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('designations_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('designations', 'last_updated_by')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->unsignedInteger('last_updated_by')->nullable()->index('designations_last_updated_by_foreign');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('discussions', 'added_by')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->unsignedInteger('added_by')->nullable()->index('discussions_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

        if (!Schema::hasColumn('discussions', 'last_updated_by')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->unsignedInteger('last_updated_by')->nullable()->index('discussions_last_updated_by_foreign');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            });
        }

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

        $this->fixUnitTypes();
        $this->fixLanguageFrontDetails();

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

    private function fixUnitTypes()
    {
        $companies = Company::select('id')->get();

        foreach ($companies as $company) {

            $modelsToUpdate = [
                Product::class,
                Invoice::class,
                Proposal::class,
                Estimate::class,
                EstimateTemplate::class,
                ProposalTemplate::class,
                RecurringInvoice::class,
            ];

            $unitData = UnitType::where('company_id', $company->id)->first();

            if ($unitData) {
                foreach ($modelsToUpdate as $model) {

                    if(Schema::hasColumn($model::getTableName(), 'unit_id')) {
                        $model::where('company_id', $company->id)
                            ->whereNull('unit_id')
                            ->update(['unit_id' => $unitData->id]);
                    }
                }
            }

        }
    }

    private function fixLanguageFrontDetails()
    {
        $frontDetail = FrontDetail::select('id', 'locale')->first();

        if ($frontDetail && is_numeric($frontDetail->locale)) {
            $languageSetting = LanguageSetting::find($frontDetail->locale);

            if ($languageSetting) {
                $frontDetail->locale = $languageSetting->language_code;
                $frontDetail->saveQuietly();
            }
        }
    }

};
