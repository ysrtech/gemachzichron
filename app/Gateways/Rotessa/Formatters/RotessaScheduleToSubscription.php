<?php


namespace App\Gateways\Rotessa\Formatters;


use App\Contracts\Formatter;
use App\Gateways\Rotessa\Frequencies;
use App\Models\Subscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RotessaScheduleToSubscription implements Formatter
{
    public function formatOutput($output): array
    {
        return [
            'gateway'            => config('gateways.rotessa.name'),
            'gateway_identifier' => $output['id'],
            'type'               => Str::contains($output['comment'], 'loan') ? Subscription::TYPE_LOAN_PAYMENT : Subscription::TYPE_MEMBERSHIP,
            'amount'             => $output['amount'],
            'start_date'         => $output['process_date'],
            'installments'       => $output['installments'],
            'frequency'          => Frequencies::$fromRotessaFrequencies[$output['frequency']],
            'active'             => $output['active'],
            'comment'            => $output['comment'],
            'gateway_data'       => Arr::only($output, ['id', 'next_process_date'])
        ];
    }
}
