<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Console\Command;

class UpdateResolvingSubscriptions extends Command
{
    protected $signature = 'subscriptions:update-resolving {--dry-run : Run without making changes}';
    protected $description = 'Scan subscription comments for "Resolves failed transaction #" pattern and update resolves_transaction field';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $pattern = '/Resolves failed transaction\s*#?(\d+)/i';
        $updated = 0;
        $resolved = 0;

        Subscription::whereNotNull('comment')
            ->where('comment', 'like', '%Resolves failed transaction%')
            ->chunk(100, function ($subscriptions) use ($pattern, $dryRun, &$updated, &$resolved) {
                foreach ($subscriptions as $subscription) {
                    if (preg_match($pattern, $subscription->comment, $matches)) {
                        $transactionId = $matches[1];
                        
                        // Find the transaction
                        $transaction = Transaction::find($transactionId);
                        
                        if ($transaction && $transaction->status == Transaction::STATUS_FAIL) {
                            $this->info("Subscription #{$subscription->id} resolves Transaction #{$transactionId}");
                            
                            if (!$dryRun) {
                                // Update subscription's resolves_transaction field
                                $subscription->update(['resolves_transaction' => $transactionId]);
                                $updated++;
                                
                                // Mark transaction as resolved
                                if (!$transaction->resolved) {
                                    $transaction->update(['resolved' => true]);
                                    $resolved++;
                                }
                            } else {
                                $updated++;
                                if (!$transaction->resolved) {
                                    $resolved++;
                                }
                            }
                        } else {
                            $this->warn("Transaction #{$transactionId} not found or not failed (Subscription #{$subscription->id})");
                        }
                    }
                }
            });

        $this->newLine();
        $this->info("Summary:");
        $this->info("Subscriptions updated: {$updated}");
        $this->info("Transactions marked as resolved: {$resolved}");
        
        if ($dryRun) {
            $this->newLine();
            $this->comment('This was a dry run. Run without --dry-run to apply changes.');
        }

        return 0;
    }
}
