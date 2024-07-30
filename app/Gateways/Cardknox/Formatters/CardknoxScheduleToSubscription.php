<?php


namespace App\Gateways\Cardknox\Formatters;


use App\Contracts\Formatter;
use App\Gateways\Cardknox\Frequencies;
use App\Gateways\Factory as GatewayFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CardknoxScheduleToSubscription implements Formatter
{
    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     */
    public function formatOutput($output): array
    {
        
        return [
            'gateway'            => GatewayFactory::CARDKNOX,
            'gateway_identifier' => $output['ScheduleId'],
            'gateway_customerid' => $output['CustomerId'],
            'start_date'         => $output['StartDate'],
            'installments'       => (isset($output['TotalPayments']) ? $output['TotalPayments'] : NULL),
            'frequency'          => Frequencies::$fromCardknoxFrequencies[strtolower($output['IntervalType'])],
            'active'             => $output['IsActive'] ?? false,
            'comment'            => $output['Description'] ?? null,
            'gateway_data'       => collect(is_array($output) ? $output : $output->json())
                ->only([
                    'ScheduleId',
                    'PaymentMethodId',
                    'Amount',
                    'NextScheduledRunTime',
                    'Revision',
                    'PaymentsProcessed',
                    
                ])
                ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
                ->toArray(),
        ];
    }
}
