<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ResolveHistoricalFailedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:resolve-historical {--dry-run : Display the operations that would be performed without actually executing them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan subscription and transaction comments for "Resolves transaction ID" pattern and mark failed transactions as resolved';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in DRY-RUN mode. No changes will be made.');
        }

        $pattern = '/Resolves failed transaction\s*#?(\d+)/i';
        $resolved_count = 0;
        $already_resolved = 0;
        $subscriptions_with_comment = 0;
        $subscriptions_with_attempts = 0;
        $subscriptions_success_attempts = 0;
        $pending_attempts = 0;
        $failed_transactions_not_found = [];
        $failed_transactions_not_failed = [];
        $transactions_with_comment = 0;
        $transactions_with_attempts = 0;
        $transactions_success_attempts = 0;

        // ===== SCAN SUBSCRIPTIONS ONLY =====
        $this->info('Scanning subscriptions...');
        $subscriptions = Subscription::whereNotNull('comment')->get();
        $this->output->progressStart($subscriptions->count());

        foreach ($subscriptions as $subscription) {
            $this->output->progressAdvance();

            if (preg_match($pattern, $subscription->comment, $matches)) {
                $subscriptions_with_comment++;
                $failed_transaction_id = (int)$matches[1];
                $failedTransaction = Transaction::find($failed_transaction_id);

                if (!$failedTransaction) {
                    $failed_transactions_not_found[] = [
                        'source' => "Subscription #{$subscription->id}",
                        'failed_transaction_id' => $failed_transaction_id
                    ];
                    continue;
                }

                if ($failedTransaction->status !== Transaction::STATUS_FAIL) {
                    $failed_transactions_not_failed[] = [
                        'transaction_id' => $failed_transaction_id,
                        'status' => $failedTransaction->status,
                        'resolved' => $failedTransaction->resolved
                    ];
                    continue;
                }

                // Skip if already has successful resolution
                if ($failedTransaction->hasResolvedSuccessfully()) {
                   
                    continue;
                }

                // Get all transactions from this subscription (successful and failed)
                $allTransactions = $subscription->transactions()
                    ->whereNotIn('type', [
                        Transaction::TYPE_MEMBERSHIP_FEE,
                        Transaction::TYPE_PROCESSING_FEE,
                        Transaction::TYPE_DECLINE_FEE
                    ])
                    ->pluck('id')
                    ->toArray();

                // Get only successful transactions for status check
                $successfulTransactions = $subscription->transactions()
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->whereNotIn('type', [
                        Transaction::TYPE_MEMBERSHIP_FEE,
                        Transaction::TYPE_PROCESSING_FEE,
                        Transaction::TYPE_DECLINE_FEE
                    ])
                    ->pluck('id')
                    ->toArray();

                // Determine status based on transactions and subscription state
                if (!empty($successfulTransactions)) {
                    $status = 'success';
                     $subscriptions_success_attempts++;
                } elseif (empty($allTransactions) && $subscription->is_active && $subscription->start_date > now()) {
                    // No transactions found but subscription is active with future start date
                    $status = 'pending';
                    $pending_attempts++;
                } else {
                    $status = 'failed';
                }

                if (!$dryRun) {
                    $failedTransaction->resolve(
                        user_id: auth()->id() ?? null,
                        subscription_ids: [$subscription->id],
                        transaction_ids: $allTransactions,
                        status: $status,
                        resolved_at: $subscription->created_at // NEW: Use subscription's created timestamp
                    );
                    
                    
                }
                if ($status === 'success') {
                        $this->line("✓ Marked transaction #{$failed_transaction_id} as resolved by subscription #{$subscription->id} with transactions: " . implode(',', $allTransactions));
                       
                    } else {
                        $this->line("⚠ Marked transaction #{$failed_transaction_id} with failed resolution attempt from subscription #{$subscription->id}");
                    }
                 $subscriptions_with_attempts++;

            }
        }

        $this->output->progressFinish();

        // ===== SCAN TRANSACTIONS =====
        $this->info('Scanning transactions...');
        $transactions = Transaction::whereNotNull('comment')->get();
        $this->output->progressStart($transactions->count());
        $transactions_with_comment = 0;
        $transactions_with_attempts = 0;

        foreach ($transactions as $transaction) {
            $this->output->progressAdvance();

            if (preg_match($pattern, $transaction->comment, $matches)) {
                $transactions_with_comment++;
                $failed_transaction_id = (int)$matches[1];
                $failedTransaction = Transaction::find($failed_transaction_id);

                if (!$failedTransaction) {
                    $failed_transactions_not_found[] = [
                        'source' => "Transaction #{$transaction->id}",
                        'failed_transaction_id' => $failed_transaction_id
                    ];
                    continue;
                }

                if ($failedTransaction->status !== Transaction::STATUS_FAIL) {
                    $failed_transactions_not_failed[] = [
                        'transaction_id' => $failed_transaction_id,
                        'status' => $failedTransaction->status
                    ];
                    continue;
                }

                // Skip if already has successful resolution
                if ($failedTransaction->hasResolvedSuccessfully()) {
                    
                    continue;
                }

                // Get parent subscription of the resolving transaction
                $resolvingSubscription = $transaction->subscription;
                
                // Determine status based on whether subscription exists and has transactions
                if ($resolvingSubscription) {
                    // Get all transactions from the parent subscription (successful and failed)
                    $allTransactions = $resolvingSubscription->transactions()
                        ->whereNotIn('type', [
                            Transaction::TYPE_MEMBERSHIP_FEE,
                            Transaction::TYPE_PROCESSING_FEE,
                            Transaction::TYPE_DECLINE_FEE
                        ])
                        ->pluck('id')
                        ->toArray();

                    // Get only successful transactions for status check
                    $successfulTransactions = $resolvingSubscription->transactions()
                        ->where('status', Transaction::STATUS_SUCCESS)
                        ->whereNotIn('type', [
                            Transaction::TYPE_MEMBERSHIP_FEE,
                            Transaction::TYPE_PROCESSING_FEE,
                            Transaction::TYPE_DECLINE_FEE
                        ])
                        ->pluck('id')
                        ->toArray();

                    // Determine status based on transactions and subscription state
                    if (!empty($successfulTransactions)) {
                        $status = 'success';
                        $transactions_success_attempts++;
                    } elseif (empty($allTransactions) && $resolvingSubscription->is_active && $resolvingSubscription->start_date > now()) {
                        // No transactions found but subscription is active with future start date
                        $status = 'pending';
                        
                    } else {
                        $status = 'failed';
                    }
                    
                    $subscription_ids = [$resolvingSubscription->id];
                    $resolved_at = $transaction->created_at;
                } else {
                    // No subscription found - check if the resolving transaction itself is successful
                    if ($transaction->status === Transaction::STATUS_SUCCESS) {
                        $status = 'success';
                        $transactions_success_attempts++;
                    } else {
                        $status = 'failed';
                    }
                    $allTransactions = [$transaction->id];
                    $subscription_ids = [];
                    $resolved_at = $transaction->created_at;
                }

                if (!$dryRun) {
                    $failedTransaction->resolve(
                        user_id: auth()->id() ?? null,
                        subscription_ids: $subscription_ids,
                        transaction_ids: $allTransactions,
                        status: $status,
                        resolved_at: $resolved_at
                    );
                    
                    
                }
                if ($status === 'success') {
                        $this->line("✓ Marked transaction #{$failed_transaction_id} as resolved by transaction #{$transaction->id} with transactions: " . implode(',', $allTransactions));
                    } elseif ($resolvingSubscription) {
                        $this->line("⚠ Marked transaction #{$failed_transaction_id} with failed resolution attempt from transaction #{$transaction->id} (subscription #{$resolvingSubscription->id})");
                    } else {
                        $this->line("⚠ Marked transaction #{$failed_transaction_id} with failed resolution attempt from transaction #{$transaction->id} (no subscription)");
                    }

                $transactions_with_attempts++;

            }
        }

        $this->output->progressFinish();

        $this->newLine(2);
        $this->info("Summary:");
        $this->line("  Subscriptions with comment: <fg=blue>$subscriptions_with_comment</>");
        $this->line("  Subscriptions with attempts added: <fg=green>$subscriptions_with_attempts</>");
        $this->line("    - Success: <fg=green>$subscriptions_success_attempts</>");
        $this->line("  Transactions with comment: <fg=blue>$transactions_with_comment</>");
        $this->line("  Transactions with attempts added: <fg=green>$transactions_with_attempts</>");
        $this->line("    - Success: <fg=green>$transactions_success_attempts</>");
        $this->line("  Total attempts marked success: <fg=green>" . ($subscriptions_success_attempts + $transactions_success_attempts) . "</>");


       

        if ($dryRun) {
            $this->newLine();
            $this->info('DRY-RUN completed. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }
}

