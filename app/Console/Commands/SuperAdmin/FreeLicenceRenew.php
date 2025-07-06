<?php

namespace App\Console\Commands\SuperAdmin;

use App\Models\Company;
use Illuminate\Console\Command;

class FreeLicenceRenew extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'free-licence-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Free licence renew.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::with('package')
            ->where('status', 'active')
            ->whereHas('package', function ($query) {
                $query->where('is_free', 1);
            })
            ->whereNotNull('licence_expire_on')
            ->where('licence_expire_on', '<', now()->format('Y-m-d'))
            ->get();

        // Set default package for license expired companies.
        foreach ($companies as $company) {
            $company->licence_expire_on = ($company->package_type == 'monthly') ? now()->addMonth()->format('Y-m-d') : now()->addYear()->format('Y-m-d');
            $company->save();
        }
    }

}
