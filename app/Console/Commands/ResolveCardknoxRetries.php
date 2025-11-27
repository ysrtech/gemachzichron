<?php

namespace App\Console\Commands;

use App\Gateways\Factory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResolveCardknoxRetries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:resolve-cardknox-retries {--dry-run : Display what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark failed Cardknox transactions as resolved when there are retry attempts in the same month, keeping only the last failed transaction unresolved';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get all failed Cardknox transactions (including already resolved ones to detect patterns)
        $allFailedTransactions = Transaction::where('gateway', Factory::CARDKNOX)
            ->where('status', Transaction::STATUS_FAIL)
            ->orderBy('subscription_id')
            ->orderBy('process_date')
            ->get();

        // Get only unresolved ones for processing
        $unresolvedTransactions = $allFailedTransactions->where('resolved', false);

        $this->info("Found {$unresolvedTransactions->count()} unresolved failed Cardknox transactions");
        $this->newLine();

        // Group ALL failed transactions by subscription_id and year-month to detect patterns
        $grouped = $allFailedTransactions->groupBy(function ($transaction) {
            $date = Carbon::parse($transaction->process_date);
            return $transaction->subscription_id . '-' . $date->format('Y-m');
        });

        $totalToResolve = 0;
        $transactionsToResolve = [];

        foreach ($grouped as $key => $group) {
            // Only process if there are multiple failed transactions in the same month for the same subscription
            if ($group->count() > 1) {
                [$subscriptionId, $yearMonth] = explode('-', $key);
                
                // Check if any transaction in this group is already marked as resolved
                $hasResolvedTransaction = $group->contains('resolved', true);
                
                // Get only unresolved transactions from this group
                $unresolvedInGroup = $group->where('resolved', false);
                
                // Skip if no unresolved transactions in this group
                if ($unresolvedInGroup->isEmpty()) {
                    continue;
                }
                
                // If any transaction is already resolved, mark all remaining unresolved ones as resolved
                // Otherwise, mark all except the last one (most recent)
                if ($hasResolvedTransaction) {
                    $toResolve = $unresolvedInGroup;
                    $this->line("Subscription #{$subscriptionId} - {$yearMonth}: ALL {$unresolvedInGroup->count()} remaining unresolved transactions to mark as resolved (found already resolved transaction)");
                } else {
                    $toResolve = $unresolvedInGroup->sortBy('process_date')->slice(0, -1);
                    $this->line("Subscription #{$subscriptionId} - {$yearMonth}: {$toResolve->count()} of {$group->count()} failed transactions to mark as resolved");
                }
                
                foreach ($toResolve as $transaction) {
                    $processDate = Carbon::parse($transaction->process_date);
                    $this->line("  - Transaction #{$transaction->id} ({$processDate->format('Y-m-d')}) - Amount: \${$transaction->amount}");
                    $transactionsToResolve[] = $transaction->id;
                }
                
                $totalToResolve += $toResolve->count();
                $this->newLine();
            }
        }

        $this->newLine();
        $this->info("Total transactions to mark as resolved: {$totalToResolve}");

        if ($totalToResolve > 0) {
            if ($dryRun) {
                $this->warn('DRY RUN: No changes were made. Run without --dry-run to apply changes.');
            } else {
                if ($this->confirm('Do you want to proceed?', true)) {
                    Transaction::whereIn('id', $transactionsToResolve)
                        ->update(['resolved' => true]);
                    
                    $this->info("Successfully marked {$totalToResolve} transactions as resolved!");
                } else {
                    $this->warn('Operation cancelled.');
                }
            }
        } else {
            $this->info('No transactions need to be resolved.');
        }

        return 0;
    }
}
