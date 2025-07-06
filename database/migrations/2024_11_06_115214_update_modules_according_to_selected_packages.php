<?php

use App\Models\Company;
use App\Models\ModuleSetting;
use App\Models\SuperAdmin\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();


        foreach($companies as $company){

            $package = Package::findOrFail($company->package_id);
            $modulesInPackage = json_decode($package->module_in_package, true);

            ModuleSetting::where('company_id', $company->id)
                ->whereIn('module_name', $modulesInPackage)
                ->update(['status' => 'active']);

            // Deactivate modules that are not in module_in_package
            ModuleSetting::where('company_id', $company->id)
                ->whereNotIn('module_name', $modulesInPackage)
                ->update(['status' => 'deactive']);

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
