<?php

namespace App\Console\Commands;

use App\Services\CsvImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data
         {--members=}
         {--memberships=}
         {--children=}
         {--loans=}
         {--rotessaCustomers=}
         {--rotessaSchedules=}
         {--cardknoxSchedules=}
         {--cardknoxTransactions=}
         {--rotessaTransactions=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from csv file in ./storage/imports';

    public function handle()
    {
        if ($members = ($this->option('members') ?? $this->ask('Please enter filename for members (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing members...');
            CsvImportService::importMembers(
                $this->getFilePath($members)
            );
            $this->info('Finished Importing members');
            $this->newLine();
        }

        if ($memberships = ($this->option('memberships') ?? $this->ask('Please enter filename for memberships (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing memberships...');
            CsvImportService::importMemberships(
                $this->getFilePath($memberships)
            );
            $this->info('Finished Importing memberships');
            $this->newLine();
        }

        if ($children = ($this->option('children') ?? $this->ask('Please enter filename for children (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing children...');
            CsvImportService::importChildren(
                $this->getFilePath($children)
            );
            $this->info('Finished Importing children');
            $this->newLine();
        }

        if ($loans = ($this->option('loans') ?? $this->ask('Please enter filename for loans (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing loans...');
            CsvImportService::importLoans(
                $this->getFilePath($loans)
            );
            $this->info('Finished Importing loans');
            $this->newLine();
        }

        if ($rotessaCustomers = ($this->option('rotessaCustomers') ?? $this->ask('Please enter filename for Rotessa customers (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing Rotessa customers...');
            CsvImportService::importRotessaCustomers(
                $this->getFilePath($rotessaCustomers)
            );
            $this->info('Finished Importing Rotessa customers');
            $this->newLine();
        }

        if ($rotessaSchedules = ($this->option('rotessaSchedules') ?? $this->ask('Please enter filename for Rotessa schedules (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing Rotessa schedules...');
            CsvImportService::importRotessaSchedules(
                $this->getFilePath($rotessaSchedules)
            );
            $this->info('Finished Importing Rotessa schedules');
            $this->newLine();
        }

        if ($cardknoxSchedules = ($this->option('cardknoxSchedules') ?? $this->ask('Please enter filename for Cardknox schedules (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing Cardknox schedules...');
            $importedCount = CsvImportService::importCardknoxSchedules(
                $this->getFilePath($cardknoxSchedules)
            );
            $this->info('Finished Importing Cardknox schedules. '.$importedCount.' schedules imported.');
            $this->newLine();
        }

        if ($cardknoxTransactions = ($this->option('cardknoxTransactions') ?? $this->ask('Please enter filename for Cardknox transactions (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing Cardknox transactions...');
            $importedCount = CsvImportService::importCardknoxLooseTransactions(
                $this->getFilePath($cardknoxTransactions)
            );
            $this->info('Finished Importing Cardknox transactions. '.$importedCount.' transactions imported.');
            $this->newLine();
        }

        if ($rotessaTransactions = ($this->option('rotessaTransactions') ?? $this->ask('Please enter filename for Rotessa transactions (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing Rotessa transactions...');
            $importedCount = CsvImportService::importRotessaLooseTransactions(
                $this->getFilePath($rotessaTransactions)
            );
            $this->info('Finished Importing Rotessa transactions. '.$importedCount.' transactions imported.');
            $this->newLine();
        }

        $this->info('Done');
    }

    private function getFilePath($filename)
    {
        $filepath = storage_path("imports/$filename");

        if (!file_exists($filepath)) {
            $this->error("file $filepath does not exist");
            exit();
        }

        return $filepath;
    }
}
