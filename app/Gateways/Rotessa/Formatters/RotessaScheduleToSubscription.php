<?php


namespace App\Gateways\Rotessa\Formatters;


use App\Contracts\Formatter;
use App\Gateways\Rotessa\Frequencies;
use Illuminate\Support\Arr;


class RotessaScheduleToSubscription implements Formatter
{
    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     */
    public function formatOutput($output): array
    {
        return [
            'gateway'            => \App\Gateways\Factory::ROTESSA,
            'gateway_identifier' => $output['id'],
            'gateway_customerid' => $this->getCustomerIdFromTransactions($output['financial_transactions']),//cause rotessa doesnt return customer id with schedules
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

    public function getCustomerIdFromTransactions($transactions){
        if(count($transactions) > 0){
            return $transactions[0]['customer_id'];
        }else{
            return null;
        }
    }
}
