<?php

namespace App\Contracts;

use App\Models\Membership;
use App\Models\PaymentMethod;

interface Gateway
{
    public function getName(): string;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @return \JetBrains\PhpStorm\ArrayShape(['gateway' => 'string', 'gateway_identifier' => 'string', 'data' => 'array']
     */
    public function createCustomer(Membership $membership, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @return \JetBrains\PhpStorm\ArrayShape(['gateway' => 'string', 'gateway_identifier' => 'string', 'data' => 'array']
     */
    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     */
    public function createSchedule(PaymentMethod $paymentMethod, array $data): array;
}
