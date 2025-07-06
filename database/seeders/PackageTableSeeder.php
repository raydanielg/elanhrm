<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use App\Models\Module;
use App\Models\SuperAdmin\Package;
use Illuminate\Database\Seeder;

class PackageTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packageModules = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->pluck('module_name')
            ->toJson();

        $currencyID = GlobalSetting::first()->currency_id;

        $defaultPackage = Package::first();
        $defaultPackage->currency_id = $currencyID;
        $defaultPackage->save();


        $package = new Package();
        $package->name = 'Starter';
        $package->currency_id = $currencyID;
        $package->description = 'Quidem deserunt nobis asperiores fuga Ullamco corporis culpa';
        $package->max_storage_size = 500;
        $package->max_file_size = 30;
        $package->max_employees = 50;
        $package->annual_price = 500;
        $package->monthly_price = 50;
        $package->billing_cycle = 10;
        $package->sort = 2;
        $package->module_in_package = $packageModules;
        $package->stripe_annual_plan_id = 'starter_annual';
        $package->stripe_monthly_plan_id = 'starter_monthly';
        $package->save();

        $package = new Package();
        $package->name = 'Medium';
        $package->currency_id = $currencyID;
        $package->description = 'Quidem deserunt nobis asperiores fuga Ullamco corporis culpa';
        $package->max_storage_size = 500;
        $package->max_file_size = 50;
        $package->max_employees = 100;
        $package->annual_price = 1000;
        $package->monthly_price = 100;
        $package->billing_cycle = 10;
        $package->sort = 3;
        $package->module_in_package = $packageModules;
        $package->stripe_annual_plan_id = 'medium_annual';
        $package->stripe_monthly_plan_id = 'medium_monthly';
        $package->save();


        $package = new Package();
        $package->name = 'Larger';
        $package->currency_id = $currencyID;
        $package->description = 'Quidem deserunt nobis asperiores fuga Ullamco corporis culpa';
        $package->max_storage_size = 500;
        $package->max_file_size = 100;
        $package->max_employees = 500;
        $package->annual_price = 5000;
        $package->monthly_price = 500;
        $package->billing_cycle = 10;
        $package->sort = 4;
        $package->module_in_package = $packageModules;
        $package->stripe_annual_plan_id = 'larger_annual';
        $package->stripe_monthly_plan_id = 'larger_monthly';
        $package->save();


    }

}
