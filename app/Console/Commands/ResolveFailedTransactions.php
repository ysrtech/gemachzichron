<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class ResolveFailedTransactions extends Command
{
    protected $signature = 'transactions:resolve:failed {--transaction=}';

    protected $description = 'Updates failed transactions to resolved';

    public function handle()
    {
        if (!$transaction = $this->option('transaction')) {
            $this->confirm('Are you sure you want to resolve all failed transactions?') or exit();
        }

        Transaction::when($transaction, fn($q) => $q->where('id', $transaction))
            ->where('status', Transaction::STATUS_FAIL)
            ->update(['status' => Transaction::STATUS_RESOLVED]);
    }
}
