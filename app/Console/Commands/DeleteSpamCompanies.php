<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class DeleteSpamCompanies extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-spam-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete spam companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete companies which registered 7 days before and email is not verified

        if (app()->environment('development')) {
            return Command::SUCCESS;
        }

        $companies = Company::join('users', 'users.company_id', '=', 'companies.id')
            ->join('user_auths', 'user_auths.id', '=', 'users.user_auth_id')
            ->select('companies.*', 'user_auths.email_verified_at', 'user_auths.id as aid')
            ->where('companies.created_at', '<=', now()->subDays(1))
            ->groupBy('companies.id')
            ->orderBy('users.id', 'asc')
            ->get();

        if ($companies) {
            $spamCompanies = $companies->filter(function ($item) {
                return is_null($item->email_verified_at);
            });

            if ($spamCompanies) {

                foreach ($spamCompanies as $company) {
                    $this->info('Deleting spam company **' . $company->company_name . '** with company id = ' . $company->id);
                    Company::where('id', $company->id)->update(['default_task_status' => null]);
                    Company::destroy($company->id);
                }

            }

        }

        return Command::SUCCESS;

    }

}
