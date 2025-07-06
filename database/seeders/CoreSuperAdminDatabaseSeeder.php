<?php

namespace Database\Seeders;

use App\Models\CustomFieldGroup;
use App\Models\GlobalSetting;
use App\Models\Module;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\PackageSetting;
use App\Models\SuperAdmin\StripeSetting;
use App\Models\SuperAdmin\SupportTicketType;
use App\Models\ThemeSetting;
use App\Scopes\CompanyScope;
use Illuminate\Database\Seeder;

class CoreSuperAdminDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->globalCurrency();
        $this->package();
        $this->packageSetting();
        $this->stripeSetting();
        $this->supportTicketType();
        $this->themeSetting();
        $this->customFieldGroup();
    }

    private function globalCurrency()
    {
        $globalCurrency = [
            [
                'currency_name' => 'Dollars',
                'currency_symbol' => '$',
                'currency_code' => 'USD',
                'exchange_rate' => 1,
                'currency_position' => 'left',
                'no_of_decimal' => 2,
                'thousand_separator' => ',',
                'decimal_separator' => '.'
            ],
            [
                'currency_name' => 'Pounds',
                'currency_symbol' => '£',
                'currency_code' => 'GBP',
                'exchange_rate' => 1,
                'currency_position' => 'left',
                'no_of_decimal' => 2,
                'thousand_separator' => ',',
                'decimal_separator' => '.'
            ],
            [
                'currency_name' => 'Euros',
                'currency_symbol' => '€',
                'currency_code' => 'EUR',
                'exchange_rate' => 1,
                'currency_position' => 'left',
                'no_of_decimal' => 2,
                'thousand_separator' => ',',
                'decimal_separator' => '.'
            ],
            [
                'currency_name' => 'Rupee',
                'currency_symbol' => '₹',
                'currency_code' => 'INR',
                'exchange_rate' => 1,
                'currency_position' => 'left',
                'no_of_decimal' => 2,
                'thousand_separator' => ',',
                'decimal_separator' => '.'
            ],
        ];

        GlobalCurrency::insert($globalCurrency);
    }

    private function package()
    {
        $packageModules = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->pluck('module_name')
            ->toJson();

        $packages = new Package();
        $packages->name = 'Default';
        $packages->description = 'Its a default package and cannot be deleted';
        $packages->annual_price = 0;
        $packages->monthly_price = 0;
        $packages->max_employees = 20;
        $packages->default = 'yes';
        $packages->is_free = 1;
        $packages->sort = 1;
        $packages->module_in_package = $packageModules;
        $packages->save();
    }

    private function packageSetting()
    {
        $packageModules = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->pluck('module_name')
            ->toJson();

        $packageSetting = new PackageSetting();
        $packageSetting->status = 'inactive';
        $packageSetting->trial_message = 'Start 30 days free trial';
        $packageSetting->no_of_days = 30;
        $packageSetting->modules = $packageModules;
        $packageSetting->save();

        $global = GlobalSetting::with('currency')->first();

        $packages = new Package();
        $packages->name = 'Trial';

        if ($global) {
            $packages->currency_id = $global->currency_id;
        }

        $packages->description = 'Its a trial package';
        $packages->max_storage_size = 500;
        $packages->annual_price = 0;
        $packages->monthly_price = 0;
        $packages->max_employees = 20;
        $packages->stripe_annual_plan_id = 'trial_plan';
        $packages->stripe_monthly_plan_id = 'trial_plan';
        $packages->default = 'trial';
        $packages->module_in_package = $packageModules;
        $packages->save();
    }

    private function stripeSetting()
    {
        $stripe = new StripeSetting();
        $stripe->api_key = null;
        $stripe->save();
    }

    private function supportTicketType()
    {
        $type = [
            ['type' => 'Question'],
            ['type' => 'Problem'],
            ['type' => 'General Query'],
        ];

        SupportTicketType::insert($type);
    }

    private function themeSetting()
    {
        $superadminTheme = ThemeSetting::where('panel', 'superadmin')->first();

        if (!$superadminTheme) {
            $superadminTheme = new ThemeSetting();
            $superadminTheme->panel = 'superadmin';
            $superadminTheme->header_color = '#ed4040';
            $superadminTheme->sidebar_color = '#292929';
            $superadminTheme->sidebar_text_color = '#cbcbcb';
            $superadminTheme->link_color = '#ffffff';
            $superadminTheme->save();
        }
    }

    private function customFieldGroup()
    {
        $customFieldGroup = CustomFieldGroup::withoutGlobalScope(CompanyScope::class)->where('name', 'Company')->first();

        if ($customFieldGroup) {
            $customFieldGroup->model = 'App\Models\Company';
            $customFieldGroup->save();
        }
        else {
            $customFieldGroup = new CustomFieldGroup();
            $customFieldGroup->name = 'Company';
            $customFieldGroup->model = 'App\Models\Company';
            $customFieldGroup->save();
        }
    }

}

