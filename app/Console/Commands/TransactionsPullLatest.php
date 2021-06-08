<?php

namespace App\Console\Commands;

use App\Jobs\PullTransactions;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class TransactionsPullLatest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:pull {gateway} {startDate} {endDate=today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls latest transactions from gateways';

    public function handle()
    {
        PullTransactions::dispatch(
            $this->argument('gateway'),
            Carbon::parse($this->argument('startDate')),
            Carbon::parse($this->argument('endDate')),
        );
    }
}
