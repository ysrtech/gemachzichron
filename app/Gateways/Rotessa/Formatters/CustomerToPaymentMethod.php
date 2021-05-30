<?php

namespace App\Gateways\Rotessa\Formatters;

use App\Contracts\Formatter;

class CustomerToPaymentMethod implements Formatter
{
    /**
     * Transforms rotessa customer response to PaymentMethod array shape (not model object)
     */
    public function formatOutput($output): array
    {
        return [
            'gateway'            => config('gateways.rotessa.name'),
            'gateway_identifier' => $output['id'],
            'data'               => $output
                ->collect()
                ->only(['account_number', 'bank_name', 'customer_type', 'identifier', 'institution_number', 'transit_number',])
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
