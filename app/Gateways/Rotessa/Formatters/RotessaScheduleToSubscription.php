<?php


namespace App\Gateways\Rotessa\Formatters;


use App\Contracts\Formatter;
use App\Gateways\Rotessa\Frequencies;
use App\Models\Subscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RotessaScheduleToSubscription implements Formatter
{
    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, type: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     */
    public function formatOutput($output): array
    {
        return [
            'gateway'            => \App\Gateways\Factory::ROTESSA,
            'gateway_identifier' => $output['id'],
            'type'               => Str::contains($output['comment'], 'loan') ? Subscription::TYPE_LOAN_PAYMENT : Subscription::TYPE_MEMBERSHIP,
            'start_date'         => $output['process_date'],
            'installments'       => $output['installments'],
            'frequency'          => Frequencies::$fromRotessaFrequencies[$output['frequency']],
            'active'             => $output['active'],
            'comment'            => $output['comment'],
            'gateway_data'       => Arr::only(is_array($output) ? $output : $output->json(), [
                'id',
                'amount',
                'next_process_date'
            ])
        ];
    }
}
