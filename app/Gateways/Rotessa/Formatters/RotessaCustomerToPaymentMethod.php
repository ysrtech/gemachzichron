<?php

namespace App\Gateways\Rotessa\Formatters;

use App\Contracts\Formatter;

class RotessaCustomerToPaymentMethod implements Formatter
{
    /**
     * @param $output
     * @return array{gateway: string, gateway_identifier: string, gateway_data: array}
     */
    public function formatOutput($output): array
    {
        return [
            'gateway'            => \App\Gateways\Factory::ROTESSA,
            'gateway_identifier' => $output['id'],
            'gateway_data'       => $output->collect()
                ->only([
                    'id',
                    'account_number',
                    'bank_name',
                    'customer_type',
                    'identifier',
                    'institution_number',
                    'transit_number'
                ])
                ->map(function ($value, $key) {
                    if (!in_array($key, ['account_number', 'transit_number', 'institution_number',])) {
                        return $value;
                    }
                    // hide sensitive info
                    return substr_replace($value, str_repeat('*', strlen($value) - 2), 0, -2);
                })
        ];
    }
}
