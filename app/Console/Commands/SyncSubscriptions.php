<?php

namespace App\Console\Commands;

use App\Exceptions\DataMismatchException;
use App\Exceptions\NotImplementedException;
use App\Gateways\Factory as GatewayFactory;
use App\Models\Subscription;
use Illuminate\Console\Command;

class SyncSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs and updates all active subscriptions with gateway schedule (not transactions)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Subscription::query()
            ->where('active', true)
            ->where('gateway', '!=', GatewayFactory::MANUAL)
            ->each(function (Subscription $subscription) {
            try {
                $subscription->syncWithGateway();
            } catch (NotImplementedException $exception) {
            } catch (DataMismatchException $exception) {
                // todo create GatewayConflict
            }
        });

        return self::SUCCESS;
    }
}
