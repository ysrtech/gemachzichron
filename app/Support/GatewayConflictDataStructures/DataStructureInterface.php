<?php

namespace App\Support\GatewayConflictDataStructures;

interface DataStructureInterface
{
    /**
     * @param $rawOutput
     * @return array{start_date: string, installments: int, frequency: string, active: boolean, comment: string, amount: string}
     */
    public static function missingSubscription($rawOutput): array;
}
