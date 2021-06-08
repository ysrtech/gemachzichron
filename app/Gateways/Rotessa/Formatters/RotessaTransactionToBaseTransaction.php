<?php


namespace App\Gateways\Rotessa\Formatters;


use App\Contracts\Formatter;
use App\Exceptions\MissingSubscriptionException;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RotessaTransactionToBaseTransaction implements Formatter
{
    protected array $statuses = [
        'Future' => 0, // shouldn't happen..
        'Pending' => Transaction::STATUS_PENDING,
        'Approved' => Transaction::STATUS_SUCCESS,
        'Declined' => Transaction::STATUS_FAIL,
    ];

    protected Subscription $subscription;

    public function formatOutput($output): array
    {
        $this->subscription = $this->getSubscriptionFromRotessaTransaction($output);

        return [
            'gateway'            => config('gateways.rotessa.name'),
            'gateway_identifier' => $output['id'],
            'amount'             => $output['amount'],
            'process_date' => $output['process_date'],
            'status' => $this->statuses[$output['status']],
            'subscription_id' => $this->subscription->id,
            'member_id' => $this->subscription->member_id,
            'type' => Transaction::TYPE_BASE_TRANSACTION,
            'comment' => $output['comment'],
            'status_message' => $output['status_reason'],
            'gateway_data' => Arr::only(is_array($output) ? $output : $output->json(), [
                'id',
                'transaction_schedule_id',
                'institution_number',
                'transit_number',
                'account_number',
                'earliest_approval_date',
                'transaction_number',
                'settlement_date',
            ]),
        ];
    }

    protected function getSubscriptionFromRotessaTransaction(array $rotessaTransaction)
    {
        $subscription = Subscription::where('gateway', config('gateways.rotessa.name'))
            ->where('gateway_identifier', $rotessaTransaction['transaction_schedule_id'])
            ->first();

        if (!$subscription) {
            Log::warning("[ROTESSA] No subscription found for rotessa schedule {$rotessaTransaction['transaction_schedule_id']}", ['rotessa_transaction' => $rotessaTransaction]);
            throw new MissingSubscriptionException("No subscription found for rotessa schedule {$rotessaTransaction['transaction_schedule_id']}");
        }

        return $subscription;
    }
}
