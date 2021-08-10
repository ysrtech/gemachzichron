<?php


namespace App\Gateways\Cardknox\Formatters;


use App\Contracts\Formatter;
use App\Exceptions\MissingSubscriptionException;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CardknoxTransactionToBaseTransaction implements Formatter
{
    protected Subscription $subscription;

    protected array $statuses = [
        'Approved' => Transaction::STATUS_SUCCESS,
        'Declined' => Transaction::STATUS_FAIL,
        'Error'    => Transaction::STATUS_FAIL,
    ];

    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, amount: string|float, process_date: string, status: int, subscription_id: string, member_id: string, type: string, comment: string, status_message: string, gateway_data: array}
     * @throws MissingSubscriptionException
     */
    public function formatOutput($output): array
    {
        $this->subscription = $this->getSubscriptionFromCardknoxTransaction($output);

        return [
            'gateway'            => \App\Gateways\Factory::CARDKNOX,
            'gateway_identifier' => $output['TransactionId'],
            'amount'             => $this->subscription->transaction_total, // cardknox doesn't return the amount 😏
            'process_date'       => $output['TransactionDate'],
            'status'             => $this->statuses[$output['GatewayStatus']],
            'subscription_id'    => $this->subscription->id,
            'member_id'          => $this->subscription->member_id,
            'type'               => Transaction::TYPE_BASE_TRANSACTION,
            'status_message'     => $output['GatewayError'],
            'gateway_data'       => Arr::only(is_array($output) ? $output : $output->json(), [
                'ScheduleId',
                'CustomerId',
                'PaymentMethodId',
                'GatewayRefNum',
            ]),
        ];
    }

    protected function getSubscriptionFromCardknoxTransaction(array $cardknoxTransaction)
    {
        $subscription = Subscription::where('gateway', \App\Gateways\Factory::CARDKNOX)
            ->where('gateway_identifier', $cardknoxTransaction['ScheduleId'])
            ->first();

        if (!$subscription) {
            Log::warning("[CARDKNOX] No subscription found for cardknox schedule {$cardknoxTransaction['ScheduleId']}", ['cardknox_transaction' => $cardknoxTransaction]);
            throw new MissingSubscriptionException("No subscription found for cardknox schedule {$cardknoxTransaction['ScheduleId']}");
        }

        return $subscription;
    }
}
