<?php

namespace App\Gateways\Rotessa;

use App\Exceptions\MissingSubscriptionException;
use App\Exceptions\NotImplementedException;
use App\Exceptions\RotessaApiException;
use App\Gateways\AbstractGateway;
use App\Gateways\Rotessa\Formatters\RotessaCustomerToPaymentMethod;
use App\Gateways\Rotessa\Formatters\RotessaScheduleToSubscription;
use App\Gateways\Rotessa\Formatters\RotessaTransactionToBaseTransaction;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class Gateway extends AbstractGateway
{
    public function __construct()
    {
        $this->baseUrl = $this->config('base_url');

        $this->defaultOptions = [
            'headers' => [
                'Authorization' => "Token token={$this->config('api_key')}",
            ]
        ];

        parent::__construct();
    }

    public function getName(): string
    {
        return \App\Gateways\Factory::ROTESSA;
    }

    public function createCustomer(Member $member, array $data): array
    {
        Log::info("[ROTESSA] creating customer (Member #$member->id)", $data);

        $response = $this->post('customers', [
            'custom_identifier'  => $member->id,
            'email'              => $member->email,
            'name'               => "{$member->first_name} {$member->last_name}",
            'bank_name'          => $data['bank_name'] ?? null,
            'transit_number'     => $data['transit_number'] ?? null,
            'institution_number' => $data['institution_number'] ?? null,
            'account_number'     => $data['account_number'] ?? null,
            'home_phone'         => $member->home_phone,
            'cell_phone'         => $member->cell_phone,
            'address'            => [
                'address_1'     => $member->address,
                'city'          => $member->city,
                'province_code' => 'QC',
                'postal_code'   => $member->postal_code,
            ]
        ]);

        Log::info("[ROTESSA] created customer (Member #$member->id)", $response->collect()->toArray());

        return $this->setFormatter(new RotessaCustomerToPaymentMethod())->format($response);
    }

    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array
    {
        $paymentMethod->load('member');

        $attributes = collect($data)
            ->only([
                'bank_name',
                'transit_number',
                'institution_number',
                'account_number',
            ])
            ->filter()
            ->merge([
                'email'   => $paymentMethod->member->email,
                'name'    => "{$paymentMethod->member->first_name} {$paymentMethod->member->last_name}",
                'home_phone' => $paymentMethod->member->home_phone,
                'cell_phone' => $paymentMethod->member->cell_phone,
                'address' => [
                    'address_1'     => $paymentMethod->member->address,
                    'city'          => $paymentMethod->member->city,
                    'province_code' => 'QC',
                    'postal_code'   => $paymentMethod->member->postal_code,
                ]
            ])
            ->toArray();

        Log::info("[ROTESSA] updating customer (Member #{$paymentMethod->member->id})", $data);

        $response = $this->patch("customers/$paymentMethod->gateway_identifier", $attributes);

        Log::info("[ROTESSA] updated customer (Member #{$paymentMethod->member->id})", $response->collect()->toArray());

        return $this->setFormatter(new RotessaCustomerToPaymentMethod())->format($response);
    }

    public function getCustomer(Member $member, $formatter = RotessaCustomerToPaymentMethod::class)
    {
        $paymentMethod = $member->paymentMethods()
            ->where('gateway', $this->getName())
            ->firstOrFail();

        $response = $this->get("customers/$paymentMethod->gateway_identifier");

        return $this->setFormatter(new $formatter())->format($response);
    }

    public function getCustomerByCustomIdentifier($customIdentifier, $formatter = RotessaCustomerToPaymentMethod::class)
    {
        $response = $this->post(
            "customers/show_with_custom_identifier",
            ['custom_identifier' => $customIdentifier]
        );

        return $this->setFormatter(new $formatter())->format($response);
    }

    public function getCustomers($query = null, $formatter = RotessaCustomerToPaymentMethod::class)
    {
        $response = $this->get('customers', $query);

        $this->setFormatter(new $formatter());

        return $response->collect()->map(fn($customer) => $this->format($customer));
    }

    public function getCustomerSchedules(PaymentMethod $paymentMethod, $query = null)
    {
        $response = $this->get("customers/$paymentMethod->gateway_identifier", $query);

        $this->setFormatter(new RotessaScheduleToSubscription);

        return $response->collect('transaction_schedules')
            ->map(fn($schedule) => $this->format($schedule));
    }

    public function createSchedule(PaymentMethod $paymentMethod, array $data): array
    {
        $response = $this->post('transaction_schedules', [
            'customer_id'  => $paymentMethod->gateway_identifier,
            'amount'       => $data['transaction_total'],
            'frequency'    => Frequencies::$toRotessaFrequencies[$data['frequency']],
            'installments' => $data['installments'],
            'process_date' => $data['start_date'],
            'comment'      => $data['comment']
        ]);

        return $this->setFormatter(new RotessaScheduleToSubscription)->format($response);
    }

    public function updateSchedule(Subscription $subscription, array $data): array
    {
        throw new NotImplementedException('Not implemented yet');

        // todo
    }

    public function getSchedule(Subscription $subscription, array $query = []): array
    {
        $response = $this->get("transaction_schedules/$subscription->gateway_identifier", $query);

        return $this->setFormatter(new RotessaScheduleToSubscription)->format($response);
    }

    public function getScheduleTransactions(Subscription $subscription, array $query = []): Collection
    {
        $response = $this->get("transaction_schedules/$subscription->gateway_identifier", $query);

        $this->setFormatter(new RotessaTransactionToBaseTransaction);

        return $response
            ->collect('financial_transactions')
            ->map(fn($transaction) => $this->format($transaction));
    }

    public function getCustomerTransactions(PaymentMethod $paymentMethod, $query = null): Collection
    {
        $response = $this->get("customers/$paymentMethod->gateway_identifier", $query);

        $this->setFormatter(new RotessaTransactionToBaseTransaction);

        return $response->collect('financial_transactions')
            ->map(function ($transaction) {
                try {
                    return $this->format($transaction);
                } catch (MissingSubscriptionException $exception) {
                    return null;
                }
            })
            ->filter();
    }

    public function getTransactions(Carbon $startDate, ?Carbon $endDate = null, $query = []): Collection
    {
        $response = $this->get('transaction_report', array_merge([
            'start_date' => $startDate->toDateString(),
            'end_date'   => ($endDate ?? now())->toDateString()
        ], $query));

        $this->setFormatter(new RotessaTransactionToBaseTransaction);

        return $response->collect()
            ->map(function ($transaction) {
                try {
                    return $this->format($transaction);
                } catch (MissingSubscriptionException $exception) {
                    return null;
                }
            })
            ->filter();
    }

    public function getTransactionsLazy(Carbon $startDate, ?Carbon $endDate = null, $query = []): LazyCollection
    {
        $this->setFormatter(new RotessaTransactionToBaseTransaction);

        return LazyCollection::make(function () use ($query, $startDate) {
            $page = 1;
            do {
                $response = $this->get('transaction_report', array_merge([
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => ($endDate ?? now())->toDateString(),
                    'page'       => $page++
                ], $query))
                    ->collect()
                    ->map(function ($transaction) {
                        try {
                            return $this->format($transaction);
                        } catch (MissingSubscriptionException $exception) {
                            return null;
                        }
                    })
                    ->filter();

                foreach ($response as $data) {
                    yield $data;
                }

                sleep(2);

            } while ($response->isNotEmpty());
        });
    }

    /**
     * @param string $url
     * @param $query
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function get(string $url, $query = null)
    {
        return $this->httpRequest->get($url, $query)->throw(function ($response, $e) {
            if ($e->getCode() == 422) {
                $message = $response->collect('errors')->reduce(fn($carry, $item) => $carry . ($carry ? ", " : '') . $item['error_message'] ?? '');
                throw new RotessaApiException($message);
            }
        });
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function post(string $url, array $data)
    {
        return $this->httpRequest->post($url, $data)->throw(function ($response, $e) {
            if ($e->getCode() == 422) {
                $message = $response->collect('errors')->reduce(fn($carry, $item) => $carry . ($carry ? ", " : '') . $item['error_message'] ?? '');
                throw new RotessaApiException($message);
            }
        });
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function patch(string $url, array $data)
    {
        return $this->httpRequest->patch($url, $data)->throw(function ($response, $e) {
            if ($e->getCode() == 422) {
                $message = $response->collect('errors')->reduce(fn($carry, $item) => $carry . ($carry ? ", " : '') . $item['error_message'] ?? '');
                throw new RotessaApiException($message);
            }
        });
    }

}
