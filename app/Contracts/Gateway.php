<?php

namespace App\Contracts;

use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface Gateway
{
    public function getName(): string;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns array of PaymentMethod attributes i.e. ['gateway' => 'string', 'gateway_identifier' => 'string', 'data' => 'array']
     */
    public function createCustomer(Member $member, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns array of PaymentMethod attributes
     */
    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns array of Subscription attributes
     */
    public function createSchedule(PaymentMethod $paymentMethod, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns array of Subscription attributes
     */
    public function updateSchedule(Subscription $subscription, array $data): array;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns Collection of array of Transaction attributes
     */
    public function getCustomerTransactions(PaymentMethod $paymentMethod, $query = null): Collection;

    /**
     * note: this will only return the first set of results if the results are paginated
     * @throws \App\Exceptions\NotImplementedException
     * @returns Collection of arrays of Transaction attributes
     */
    public function getTransactions(Carbon $startDate, ?Carbon $endDate = null, $query = []): Collection;

    /**
     * @throws \App\Exceptions\NotImplementedException
     * @returns LazyCollection of arrays of Transaction attributes
     */
    public function getTransactionsLazy(Carbon $startDate, ?Carbon $endDate = null, $query = []): LazyCollection;
}
