<?php

use App\Models\SuperAdmin\GlobalInvoiceSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableGateway = 'global_invoice_settings';
        if (!Schema::hasColumn($tableGateway, 'billing_name')) {
            Schema::table($tableGateway, function (Blueprint $table) {
                $table->string('billing_name')->nullable()->after('id');
                $table->string('billing_address')->nullable()->after('billing_name');
                $table->string('billing_tax_name')->nullable()->after('billing_address');
                $table->string('billing_tax_id')->nullable()->after('billing_tax_name');
            });


            $setting = global_setting();

            if($setting){
                $invoice = GlobalInvoiceSetting::first();
                $invoice->billing_name = $setting->global_app_name;
                $invoice->billing_address = $setting->address;
                $invoice->save();
            }

        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
