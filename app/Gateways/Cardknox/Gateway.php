<?php

namespace App\Gateways\Cardknox;

use App\Exceptions\CardknoxApiException;
use App\Exceptions\MissingSubscriptionException;
use App\Gateways\AbstractGateway;
use App\Gateways\Cardknox\Formatters\CardknoxScheduleToSubscription;
use App\Gateways\Cardknox\Formatters\CardknoxTransactionToBaseTransaction;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class Gateway extends AbstractGateway
{
    const RESULT_SUCCESS = 'S';
    const RESULT_ERROR = 'E';

    const SOFTWARE_NAME = 'Gemach Hakehilos';
    const SOFTWARE_VERSION = '0.1';

    public function __construct()
    {
        $this->baseUrl = $this->config('subscription_base_url');
        $this->defaultOptions = [
            'headers' => [
                'Authorization' => $this->config('api_key'),
                'Content-Type' => 'application/json',
                'X-Recurring-Api-Version' => $this->config('subscription_api_version'),
            ],
            'json' => [
                "SoftwareName" => self::SOFTWARE_NAME,
                "SoftwareVersion" => self::SOFTWARE_VERSION,
            ],
        ];

        parent::__construct();
    }

    public function getName(): string
    {
        return \App\Gateways\Factory::CARDKNOX;
    }

    // region Customers/Payment Methods
    public function createCustomer(Member $member, array $data): array
    {
        Log::info("[CARDKNOX] creating customer (Member #$member->id)");

        $customer = $this->post('CreateCustomer', [
            'CustomerNumber' => $member->id,
            'Email'          => $member->email,
            'BillFirstName'  => $member->first_name,
            'BillLastName'   => $member->last_name,
            'BillPhone'      => $member->home_phone,
            'BillMobile'     => $member->cell_phone,
            'BillStreet'     => $member->address,
            'BillCity'       => $member->city,
            'BillState'      => 'QC',
            'BillZip'        => $member->postal_code,
        ]);

        Log::info("[CARDKNOX] created customer (Member #$member->id)", $customer->collect()->toArray());

        $paymentMethod = $this->createPaymentMethod($member, $customer['CustomerId'], $data);

        return [
            'gateway'            => \App\Gateways\Factory::CARDKNOX,
            'gateway_identifier' => $customer['CustomerId'],
            'gateway_data'       => $paymentMethod->toArray()
        ];
    }

    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array
    {
        $paymentMethod->load('member');

        Log::info("[CARDKNOX] updating customer (Member #{$paymentMethod->member->id})");

        $this->post('UpdateCustomer', [
            'CustomerId'     => $paymentMethod->gateway_identifier,
            'Revision'       => $this->post('GetCustomer', ['CustomerId' => $paymentMethod->gateway_identifier])->json('Revision'),
            'DefaultPaymentMethodId' => $paymentMethod->gateway_data['PaymentMethodId'],
            'CustomerNumber' => $paymentMethod->member->id,
            'Email'          => $paymentMethod->member->email,
            'BillFirstName'  => $paymentMethod->member->first_name,
            'BillLastName'   => $paymentMethod->member->last_name,
            'BillPhone'      => $paymentMethod->member->home_phone,
            'BillMobile'     => $paymentMethod->member->cell_phone,
            'BillStreet'     => $paymentMethod->member->address,
            'BillCity'       => $paymentMethod->member->city,
            'BillState'      => 'QC',
            'BillZip'        => $paymentMethod->member->postal_code,
        ]);

        return [
            'gateway' => $this->getName(),
            'gateway_identifier' => $paymentMethod->gateway_identifier,
            'gateway_data' => empty($data) ? $paymentMethod->gateway_data : $this->updatePaymentMethod($paymentMethod, $data)
        ];
    }

    public function createPaymentMethod(Member $member, string $customerId, array $data)
    {
        Log::info("[CARDKNOX] requesting token for member #$member->id (cardknox customer $customerId)");

        $token = $this->requestNewToken($data);

        Log::info("[CARDKNOX] creating payment method for member #$member->id (cardknox customer $customerId) using token $token");

        $paymentMethodCreate = $this->post('CreatePaymentMethod', [
            'CustomerId' => $customerId,
            'Token' => $token,
            'TokenType' => 'cc',
            'Exp' => $data['card_expiry'],
//            'SetAsDefault' => true
        ]);

        $paymentMethodGet = $this->post('GetPaymentMethod', [
            'PaymentMethodId' => $paymentMethodCreate['PaymentMethodId']
        ]);

        return $paymentMethodGet->collect()->only([
            'PaymentMethodId',
            'Exp',
            'Issuer',
            'MaskedCardNumber',
            'Revision',
        ]);
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data)
    {
//        Log::info("[CARDKNOX] requesting token for cardknox payment method $paymentMethod->gateway_identifier");
//
//        $token = $this->requestNewToken($data);
//
//        Log::info("[CARDKNOX] updating payment method for cardknox payment method $paymentMethod->gateway_identifier using token $token");
//
//        // Not exactly sure what UpdatePaymentMethod does if you can't pass in Token... Cardknox api...
//        $paymentMethodUpdate = $this->post('UpdatePaymentMethod', [
//            'PaymentMethodId' => $paymentMethod->gateway_data['PaymentMethodId'],
//            'Token' => $token,
//            'TokenType' => 'cc',
//            'Exp' => $data['card_expiry'],
//            'Revision' => $paymentMethod->gateway_data['Revision'] ?? $this->post('GetPaymentMethod', ['PaymentMethodId' => $paymentMethod->gateway_identifier])->json('Revision'),
//            'SetAsDefault' => true
//        ]);
//
//        $paymentMethodGetUpdated = $this->post('GetPaymentMethod', [
//            'PaymentMethodId' => $paymentMethodUpdate['PaymentMethodId']
//        ]);
//
//        return $paymentMethodGetUpdated->collect()->only([
//            'PaymentMethodId',
//            'Exp',
//            'Issuer',
//            'MaskedCardNumber',
//            'Revision',
//        ]);

        // Since the above doesn't work we'll need to delete and create a new payment method
        $this->deletePaymentMethod($paymentMethod);
        return $this->createPaymentMethod($paymentMethod->member, $paymentMethod->gateway_identifier, $data);
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->post('DeletePaymentMethod', [
            'PaymentMethodId' => $paymentMethod->gateway_data['PaymentMethodId'],
        ]);
    }

    public function removeSchedule(Subscription $subscription)
    {
        if ($subscription->active) {
            $this->deactivateSchedule($subscription);
        }

        try{
                $this->post('DeleteSchedule', [
                    'ScheduleId' => $subscription->gateway_identifier,
                ]);
                $subscription->setAsDeletedFromGateway();
        }catch(Exception  $e){
            return null;
        }
    }

    public function cancelSchedule(Subscription $subscription)
    {
        
        $this->deactivateSchedule($subscription);
        $subscription->setAsInactive();
        

    }
    // endregion

    // region Schedules
    public function createSchedule(PaymentMethod $paymentMethod, array $data): array
    {
        $response = $this->post('CreateSchedule', [
            'CustomerId'    => $paymentMethod->gateway_identifier,
            'Amount'        => $data['transaction_total'],
            'IntervalType'  => Frequencies::$toCardknoxFrequencies[$data['frequency']],
            'TotalPayments' => $data['installments'],
            'StartDate'     => $data['start_date'],
            'Description'   => $data['comment'],
            'FailedTransactionRetryTimes' => 0
        ]);

        $schedule = $this->post('GetSchedule', [
            'ScheduleId' => $response['ScheduleId']
        ]);

        return $this->setFormatter(new CardknoxScheduleToSubscription)->format($schedule);
    }

    public function updateSchedule(Subscription $subscription, array $data): array
    {
        $revision = $subscription->gateway_data['revision'];

        if (!$subscription->active) {
            // activate temporarily - Cardknox does not allow update of inactive schedules
            $this->activateSchedule($subscription);
            $revision++;
        }

        $this->post('UpdateSchedule', [
            'ScheduleId'      => $subscription->gateway_identifier,
            'Amount'          => $data['transaction_total'],
            'StartDate'       => $data['start_date'], // can only be changed if schedule did not begin yet
            'Description'     => $data['comment'],
            'TotalPayments'   => $data['installments'],
            'CalendarCulture' => 'Gregorian',
            'Revision'        => $revision
        ]);

        if ((isset($data['active']) && $data['active'] === false) || (!isset($data['active']) && !$subscription->active)) {
            $this->deactivateSchedule($subscription);
        }

        return $this->getSchedule($subscription);
    }

    public function activateSchedule(Subscription $subscription)
    {
        $this->post('EnableSchedule', [
            'ScheduleId' => $subscription->gateway_identifier
        ]);
    }

    public function deactivateSchedule(Subscription $subscription)
    {
        $this->post('DisableSchedule', [
            'ScheduleId' => $subscription->gateway_identifier
        ]);
    }

    public function getSchedule(Subscription $subscription, array $query = []): array
    {
        $schedule = $this->post('GetSchedule', [
            'ScheduleId' => $subscription->gateway_identifier
        ]);

        return $this->setFormatter(new CardknoxScheduleToSubscription)->format($schedule);
    }

    public function getSchedules(array $query = []): array
    {
        $this->setFormatter(new CardknoxScheduleToSubscription);

        return $this
            ->post('ListSchedules', $query)
            ->collect('Schedules')
            ->map(fn($schedule) => $this->format($schedule));
    }

    public function getCustomerSchedules(PaymentMethod $paymentMethod, $query = null)
    {
        return $this->getSchedules(array_merge_recursive($query, [
            'Filters' => [
                'CustomerId' => $paymentMethod->gateway_identifier
            ]
        ]));
    }
    // endregion

    // region Transactions
    public function getTransactions(Carbon $startDate, ?Carbon $endDate = null, $query = []): Collection
    {
        $this->setFormatter(new CardknoxTransactionToBaseTransaction);

        return $this
            ->post('ListTransactions', array_merge_recursive([
                'Filters' => [
                    'FromDate' => $startDate->toDateString(),
                    'ToDate'   => ($endDate ?? now())->toDateString(),
                ]
            ], $query))
            ->collect('Transactions')
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
        $this->setFormatter(new CardknoxTransactionToBaseTransaction);

        return LazyCollection::make(function () use ($query, $startDate) {
            $nextToken = '';
            do {
                $response = $this
                    ->post('ListTransactions', array_merge_recursive([
                        'Filters' => [
                            'FromDate' => $startDate->toDateString(),
                            'ToDate'   => ($endDate ?? now())->toDateString(),
                        ],
                        'NextToken' => $nextToken,
                        'PageSize' => 20,
                    ], $query))
                    ->collect()
                    ->pipe(function ($collection) use (&$nextToken) {
                        $nextToken = $collection['NextToken'] ?? null;
                        return collect($collection->get('Transactions'));
                    })
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

            } while ($response->isNotEmpty() && !is_null($nextToken));
        });
    }

    public function getScheduleTransactions(Subscription $subscription, array $query = []): Collection
    {
        return $this->getTransactions(
            $query['startDate'] ?? Carbon::parse(0),
            $query['endDate'] ?? null,
            ['Filters' => array_merge($query['Filters'] ?? [], ['ScheduleId' => $subscription->gateway_identifier])]
        );
    }

    public function getCustomerTransactions(PaymentMethod $paymentMethod, $query = null): Collection
    {
        return $this->getTransactions(
            $query['startDate'] ?? Carbon::parse(0),
            $query['endDate'] ?? null,
            ['Filters' => array_merge($query['Filters'] ?? [], ['CustomerId' => $paymentMethod->gateway_identifier])]
        );
    }
    // endregion

    protected function requestNewToken(array $data): string
    {
        $response = Http::post($this->config('transaction_base_url'), [
            'xCommand' => 'cc:save',
            'xKey' => $this->config('api_key'),
            'xVersion' => $this->config('transaction_api_version'),
            'xSoftwareName' => self::SOFTWARE_NAME,
            'xSoftwareVersion' => self::SOFTWARE_VERSION,
            'xCardNum' => $data['card_number'],
            'xExp' => $data['card_expiry'],
        ])->throw();

        if ($response['xResult'] == self::RESULT_ERROR) {
            throw new CardknoxApiException($response['xError']);
        }

        return $response['xToken'];
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws CardknoxApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function post(string $url, array $data)
    {
        $response = $this->httpRequest->post($url, $data)->throw();

        if ($response['Result'] == self::RESULT_ERROR) {
            throw new CardknoxApiException($response['Error']);
        }

        return $response;
    }
}
