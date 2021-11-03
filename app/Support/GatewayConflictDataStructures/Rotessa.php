<?php

namespace App\Support\GatewayConflictDataStructures;

use App\Gateways\Rotessa\Frequencies;

class Rotessa implements DataStructureInterface
{
    /**
     * @inheritDoc
     */
    public static function missingSubscription($rawOutput): array
    {
        return [
            'start_date'   => $rawOutput['process_date'],
            'installments' => $rawOutput['installments'],
            'frequency'    => Frequencies::$fromRotessaFrequencies[$rawOutput['frequency']],
            'active'       => $rawOutput['active'],
            'comment'      => $rawOutput['comment'],
            'amount'       => $rawOutput['amount'],
        ];
    }
}
