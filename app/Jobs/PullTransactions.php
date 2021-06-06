<?php

namespace App\Jobs;

use App\Facades\Gateway;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class PullTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $gateway;
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected array $query;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $gateway, Carbon $startDate, ?Carbon $endDate = null, array $query = [])
    {
        $this->gateway = $gateway;
        $this->startDate = $startDate;
        $this->endDate = $endDate ?? today();
        $this->query = $query;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $gateway = Gateway::initialize($this->gateway);

        $gateway->getTransactionsLazy($this->startDate, $this->endDate, $this->query)
            ->each(function ($gatewayTransaction) use ($gateway) {
                $transactions = Transaction::where([
                    'gateway' => $gateway->getName(),
                    'gateway_identifier' => $gatewayTransaction['gateway_identifier'],
                ])->get();

                $baseTransaction = $transactions->isEmpty()
                    ? Transaction::create($gatewayTransaction)
                    : $transactions->firstWhere('type', Transaction::TYPE_BASE_TRANSACTION);

                if (!$baseTransaction) return;

                $attributes = Arr::only($gatewayTransaction, [
                    'process_date',
                    'status',
                    'comment',
                    'error_message',
                    'gateway_data',
                ]);

                if ($gatewayTransaction['status'] == Transaction::STATUS_SUCCESS) {
                    $baseTransaction->split($attributes);
                } else {
                    $baseTransaction->update($attributes);
                }
            });
    }
}
