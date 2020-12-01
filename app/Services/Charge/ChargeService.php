<?php

namespace App\Services\Charge;

abstract class ChargeService implements Chargeable
{
    protected int $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }
}
