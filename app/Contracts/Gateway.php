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
     * @param Member $member
     * @param array $data
     * @return array{gateway: string, gateway_identifier: string, gateway_data: array}
     * @throws \App\Exceptions\NotImplementedException
     */
    public function createCustomer(Member $member, array $data): array;

    /**
     * @param PaymentMethod $paymentMethod
     * @param array $data
     * @return array{gateway: string, gateway_identifier: string, gateway_data: array}
     * @throws \App\Exceptions\NotImplementedException
     */
    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array;

    /**
     * @param PaymentMethod $paymentMethod
     * @param array $data
     * @return array{gateway: string, gateway_identifier: string, type: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     * @throws \App\Exceptions\NotImplementedException
     */
    public function createSchedule(PaymentMethod $paymentMethod, array $data): array;

    /**
     * @param Subscription $subscription
     * @param array $data
     * @return array{gateway: string, gateway_identifier: string, type: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     * @throws \App\Exceptions\NotImplementedException
     */
    public function updateSchedule(Subscription $subscription, array $data): array;

    /**
     * @param Subscription $subscription
     * @param array $query
     * @return array{gateway: string, gateway_identifier: string, type: string, start_date: string, installments: int, frequency: string, active: bool, comment: string, gateway_data: array}
     */
    public function getSchedule(Subscription $subscription, array $query = []): array;

    public function getScheduleTransactions(Subscription $subscription, array $query = []): Collection;

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
