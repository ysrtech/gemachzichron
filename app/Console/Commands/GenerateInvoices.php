<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate';

    protected $description = 'Generates invoices for subscriptions scheduled for today';

    public function handle()
    {
        $missingDays = now()->day == 1 ? 31 - now()->subDay()->day : 0;

        $isEvenMonth = now()->month % 2 === 0;

        Subscription::active()
            ->where(function ($query) use ($missingDays, $isEvenMonth) {
                $query
                    ->where(function ($subQuery) use ($isEvenMonth) {
                        $subQuery
                            ->where('process_day', now()->day)
                            ->when(!$isEvenMonth, fn($query) => $subQuery->where('frequency', '!=', Subscription::FREQUENCY_BIMONTHLY));
                    })
                    ->when((bool)$missingDays, fn($subQuery) => $subQuery->orWhere(function ($subSubQuery) use ($missingDays, $isEvenMonth) {
                        $subSubQuery
                            ->whereIn('process_day', range(31 - $missingDays, 31))
                            ->when($isEvenMonth, fn($query) => $query->where('frequency', '!=', Subscription::FREQUENCY_BIMONTHLY));
                    }));
            })
            ->each(function (Subscription $subscription) {
                $this->info("Creating invoice for subscription {$subscription->id}");
                $subscription->createInvoice();
            });
    }
}
