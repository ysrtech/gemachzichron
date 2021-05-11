<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Membership;
use App\Services\MembersImportService;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:members {filename} {--memberships=} {--children=} {--loans=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import members from csv file in ./storage/imports';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment('Importing members...');
        MembersImportService::importMembers($this->getFilePath($this->argument('filename')));
        $this->info('Finished Importing members');
        $this->newLine();

        if ($memberships = ($this->option('memberships') ?? $this->ask('Please enter filename for memberships (or empty to skip) then [ENTER]'))) {
            $this->callSilently('db:seed', ['class' => 'PlanTypeSeeder']);
            $this->comment('Importing memberships...');
            MembersImportService::importMemberships(
                $this->getFilePath($memberships)
            );
            $this->info('Finished Importing memberships');
            $this->newLine();
        }

        if ($children = ($this->option('children') ?? $this->ask('Please enter filename for children (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing children...');
            MembersImportService::importChildren(
                $this->getFilePath($children)
            );
            $this->info('Finished Importing children');
            $this->newLine();
        }

        if ($loans = ($this->option('loans') ?? $this->ask('Please enter filename for loans (or empty to skip) then [ENTER]'))) {
            $this->comment('Importing loans...');
            MembersImportService::importLoans(
                $this->getFilePath($loans)
            );
            $this->info('Finished Importing loans');
            $this->newLine();
        }

        $this->info('Done');
    }

    private function getFilePath($filename) {
        $filepath = storage_path("imports/$filename");

        if (!file_exists($filepath)) {
            $this->error("file $filepath does not exist");
            exit();
        }

        return $filepath;
    }
}
