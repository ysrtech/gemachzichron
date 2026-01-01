<?php

namespace App\Console\Commands;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Gateways\Factory as GatewayFactory;
use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AdjustLoanPaymentSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:adjust-loan-payments {--dry-run : Run without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adjusts loan payment subscriptions: if a member has multiple monthly loan subscriptions but none at $350, updates the oldest active $250 subscription to $350';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }
        
        $this->info('Starting loan payment subscription adjustments...');
        
        $adjustedCount = 0;
        $errorCount = 0;

        // Find members with more than one monthly loan payment subscription (type = loan payment, frequency = monthly)
        Member::whereHas('subscriptions', function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      });
            }, '>', 1)
            ->with(['subscriptions' => function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      })
                      ->orderBy('created_at', 'asc');
            }])
            ->get()
            ->each(function (Member $member) use (&$adjustedCount, &$errorCount, $dryRun) {
                try {
                    // Use the member function for both dry-run and actual adjustment
                    $result = $member->adjustLoanPaymentSubscriptions($dryRun);

                    if ($result['success']) {
                        if ($dryRun) {
                            $this->info("Found subscription #{$result['subscription_id']} for member #{$member->id} ({$member->first_name} {$member->last_name})");
                        }
                        $this->line("✓ " . $result['message'] . ($dryRun ? '' : " for member #{$member->id} ({$member->first_name} {$member->last_name})"));
                        $adjustedCount++;
                    }
                    // Silently skip members that don't qualify (handled by the query already)

                } catch (\Exception $exception) {
					$this->error("Error processing member #{$member->id}: " . $exception->getMessage());
					$errorCount++;
				}
			});

        $this->info("Completed! Adjusted {$adjustedCount} subscription(s) with {$errorCount} error(s).");

        return self::SUCCESS;
    }
}
