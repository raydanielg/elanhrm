<?php

use App\Models\Company;
use App\Scopes\ActiveScope;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration {

    /**
     * Run the migrations.
     * We have changed the file_name as 2018 for the purpose of modules
     *
     * @return true
     */
    public function up()
    {
        if (!isWorksuiteSaas()) {
            return true;
        }

        try {
            $companyCount = Company::withoutGlobalScope(ActiveScope::class)->count();

            // Do not run below command if company is not present in database
            if ($companyCount == 0) {
                return true;
            }
        }catch (\Exception $e){
            return true;
        }



        // Fix collation
        $this->fixCollation();


    }

    private function fixCollation(): void
    {

        $tableName = 'accept_estimates';

        // Check if table exist. If table exist that means it's running for old database
        if (!Schema::hasTable($tableName)) {
            return;
        }

        $collation = DB::connection()
            ->getDoctrineSchemaManager()
            ->listTableDetails($tableName)
            ->getOptions()['collation'];

        // Check if collation is utf8mb4_unicode_ci for table accept_estimates if not and proceed
        if ($collation !== config('database.connections.mysql.collation')) {

            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

            $totalTables = count($tables);
            $charset = 'utf8mb4';
            $collate = $charset . '_unicode_ci';

            foreach ($tables as $key => $table) {
                $console = 'Collation Change Remaining: ' . ($totalTables - $key) . ' ' . $table;
                $output = new ConsoleOutput();
                $output->writeln('<info>'.$console.'</info>');
                $query = "ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset COLLATE $collate";
                DB::statement($query);
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
        // Removed all drop code to minimize the file size.
    }

};
