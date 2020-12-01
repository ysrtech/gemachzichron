<?php


namespace App\Services\Charge;

interface Chargeable
{
    /** @throws \App\Exceptions\FailedPaymentException */
    public function charge(): bool;
}
