<?php

namespace App\Support\GatewayConflictDataStructures;

use App\Gateways\Cardknox\Frequencies;

class Cardknox implements DataStructureInterface
{
    /**
     * @inheritDoc
     */
    public static function missingSubscription($rawOutput): array
    {
        return [
            'start_date'   => $rawOutput['StartDate'],
            'installments' => $rawOutput['TotalPayments'],
            'frequency'    => Frequencies::$fromCardknoxFrequencies[strtolower($rawOutput['IntervalType'])],
            'active'       => $rawOutput['IsActive'] ?? false,
            'comment'      => $rawOutput['Description'] ?? null,
            'amount'       => $rawOutput['Amount'],
        ];
    }
}
