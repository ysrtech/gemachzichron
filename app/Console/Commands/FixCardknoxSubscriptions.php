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
    protected $signature = 'fixcardknoxsubscriptions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs all Cardknox subscribtions even inactive or marked deleted in gateway subscriptions with gateway schedule (not transactions)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Subscription::query()
            ->where('gateway', '=', GatewayFactory::CARDKNOX)
            ->each(function (Subscription $subscription) {
                try {
                    $this->line("Syncing subscription #$subscription->id");
                    $subscription->syncWithGateway();
                } catch (NotImplementedException $exception) {
                    
                }
                
            });

        $this->info('Done!');

        return self::SUCCESS;
    }
}
