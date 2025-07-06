<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\Package;
use Illuminate\Database\Seeder;

class NonSaasToSaasSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return true
     */
    public function run()
    {
        if (!config('app.non_saas_to_saas_enabled')) {
            return true;
        }

        config(['app.seeding' => true]);

        $this->seedInitialData();

        config(['app.seeding' => false]);

        cache()->flush();
    }

    protected function seedInitialData()
    {
        $this->call([
            FrontSeeder::class,
            GlobalCurrencyFormatSetting::class,
            CoreSuperAdminDatabaseSeeder::class,
            SuperAdminRoleTableSeeder::class,
            SuperAdminUsersTableSeeder::class,
            PackageTableSeeder::class,
        ]);

        $this->updateCompanyPackage();
        $this->updateGlobalSettingCurrency();
        GlobalPaymentGatewayCredentials::create();
    }

    protected function updateCompanyPackage()
    {
        $company = Company::first();
        $company->package_id = Package::first()->id;
        $company->save();
    }

    protected function updateGlobalSettingCurrency()
    {
        $globalSetting = \App\Models\GlobalSetting::first();
        $globalSetting->currency_id = GlobalCurrency::first()->id;
        $globalSetting->save();
    }

}
