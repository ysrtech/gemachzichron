<?php


namespace App\Gateways\Cardknox\Formatters;


use App\Contracts\Formatter;
use App\Gateways\Cardknox\Frequencies;
use Illuminate\Support\Arr;

class CardknoxScheduleToSubscription implements Formatter
{
    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     */
    public function formatOutput($output): array
    {
        return [
            'gateway'            => \App\Gateways\Factory::CARDKNOX,
            'gateway_identifier' => $output['ScheduleId'],
            'start_date'         => $output['StartDate'],
            'installments'       => $output['TotalPayments'],
            'frequency'          => Frequencies::$fromCardknoxFrequencies[strtolower($output['IntervalType'])],
            'active'             => $output['IsActive'],
            'comment'            => $output['Description'] ?? null,
            'gateway_data'       => Arr::only(is_array($output) ? $output : $output->json(), [
                'ScheduleId',
                'Amount',
                'NextScheduledRunTime',
                'Revision',
                'PaymentsProcessed'
            ])
        ];
    }
}
