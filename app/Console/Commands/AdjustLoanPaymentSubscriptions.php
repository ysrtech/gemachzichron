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
                $monthlyLoanSubscriptions = $member->subscriptions
                    ->where('type', Subscription::TYPE_LOAN_PAYMENT)
                    ->where('frequency', Subscription::FREQUENCY_MONTHLY);

                // Check if member has more than one monthly loan subscription
                if ($monthlyLoanSubscriptions->count() <= 1) {
                    return;
                }

                // Check if at least one is active
                $hasActiveSubscription = $monthlyLoanSubscriptions->where('active', true)->isNotEmpty();
                if (!$hasActiveSubscription) {
                    return;
                }

                // Check if any subscription has amount of $350 and is active
                $has350Subscription = $monthlyLoanSubscriptions->where('amount', 350)->where('active', true)->isNotEmpty();
                if ($has350Subscription) {
                    return;
                }

                // Find the oldest active $250 subscription
                $subscriptionToUpdate = $monthlyLoanSubscriptions
                    ->where('active', true)
                    ->where('amount', 250)
                    ->sortBy('created_at')
                    ->first();

                if (!$subscriptionToUpdate) {
                    $this->line("Member #{$member->id} ({$member->first_name} {$member->last_name}) has multiple monthly loan subscriptions but no active \$250 subscription to update");
                    return;
                }

                try {
                    $this->info("Found subscription #{$subscriptionToUpdate->id} for member #{$member->id} ({$member->first_name} {$member->last_name}) - Amount: \${$subscriptionToUpdate->amount}, Gateway: {$subscriptionToUpdate->gateway}");

                    if ($dryRun) {
                        $this->line("  [DRY RUN] Would update from \${$subscriptionToUpdate->amount} to \$350");
                        $adjustedCount++;
                    } else {
                        $newAmount = 350;
                        $transactionTotal = $newAmount 
                            + $subscriptionToUpdate->membership_fee 
                            + $subscriptionToUpdate->processing_fee 
                            + $subscriptionToUpdate->decline_fee;

                        $updateData = [
                            'amount' => $newAmount,
                            'transaction_total' => $transactionTotal,
                            'start_date' => $subscriptionToUpdate->start_date,
                            'installments' => $subscriptionToUpdate->installments,
                            'membership_fee' => $subscriptionToUpdate->membership_fee,
                            'processing_fee' => $subscriptionToUpdate->processing_fee,
                            'decline_fee' => $subscriptionToUpdate->decline_fee,
                        ];

                        // Update in gateway if not manual and not deleted from gateway
                        if ($subscriptionToUpdate->gateway != GatewayFactory::MANUAL && !$subscriptionToUpdate->isDeletedInGateway()) {
                            try {
                                $gatewayResponse = Gateway::initialize($subscriptionToUpdate->gateway)->updateSchedule(
                                    $subscriptionToUpdate,
                                    $updateData
                                );
                                $updateData = array_merge($updateData, $gatewayResponse);
                            } catch (NotImplementedException $exception) {
                                $this->error("Gateway {$subscriptionToUpdate->gateway} does not support updating subscriptions");
                                Log::error("Failed to update subscription #{$subscriptionToUpdate->id} in gateway: " . $exception->getMessage());
                                $errorCount++;
                                return;
                            } catch (\Exception $exception) {
                                $this->error("Failed to update subscription #{$subscriptionToUpdate->id} in gateway: " . $exception->getMessage());
                                Log::error("Failed to update subscription #{$subscriptionToUpdate->id} in gateway: " . $exception->getMessage());
                                $errorCount++;
                                return;
                            }
                        }

                        // Update subscription in database
                        $subscriptionToUpdate->update([
                            'amount' => $newAmount,
                            'membership_fee' => $updateData['membership_fee'],
                            'processing_fee' => $updateData['processing_fee'],
                            'decline_fee' => $updateData['decline_fee'],
                        ]);

                        $this->line("✓ Successfully updated subscription #{$subscriptionToUpdate->id} to \$350");
                        Log::info("Adjusted loan payment subscription #{$subscriptionToUpdate->id} from \$250 to \$350 for member #{$member->id}");
                        $adjustedCount++;
                    }

                } catch (\Exception $exception) {
						$this->error("Error processing subscription #{$subscriptionToUpdate->id}: " . $exception->getMessage());
						$errorCount++;
					}
				});

        $this->info("Completed! Adjusted {$adjustedCount} subscription(s) with {$errorCount} error(s).");

        return self::SUCCESS;
    }
}
