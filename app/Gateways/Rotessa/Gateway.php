<?php

namespace App\Gateways\Rotessa;

use App\Gateways\AbstractGateway;
use App\Gateways\Rotessa\Formatters\CustomerToPaymentMethod;
use App\Gateways\Rotessa\Formatters\ScheduleToSubscription;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class Gateway extends AbstractGateway
{
    protected array $transactionStatuses = [
        'Approved' => Transaction::STATUS_SUCCESS,
        'Pending'  => Transaction::STATUS_PENDING,
        'Declined' => Transaction::STATUS_FAIL,
    ];

    public function __construct()
    {
        $this->baseUrl = config('gateways.rotessa.base_url');
        $this->defaultOptions = [
            'headers' => [
                'Authorization' => 'Token token=' . config('gateways.rotessa.api_key'),
            ]
        ];

        parent::__construct();
    }

    public function getName(): string
    {
        return config('gateways.rotessa.name');
    }

    public function createCustomer(Membership $membership, array $data): array
    {
        Log::info("[ROTESSA] creating customer (Member #{$membership->member->id})", $data);

        $response = $this->httpRequest->post('customers', [
            'custom_identifier'  => $membership->member->id,
            'email'              => $membership->member->email,
            'name'               => "{$membership->member->first_name} {$membership->member->last_name}",
            'bank_name'          => $data['bank_name'] ?? null,
            'transit_number'     => $data['transit_number'] ?? null,
            'institution_number' => $data['institution_number'] ?? null,
            'account_number'     => $data['account_number'] ?? null,
            'address'            => [
                'address_1'     => $membership->member->address,
                'city'          => $membership->member->city,
                'province_code' => 'QC',
                'postal_code'   => $membership->member->postal_code,
            ]
        ])->throw();

        Log::info("[ROTESSA] created customer (Member #{$membership->member->id})", $response->collect()->toArray());

        return $this->setFormatter(new CustomerToPaymentMethod())->format($response);
    }

    public function updateCustomer(PaymentMethod $paymentMethod, array $data): array
    {
        $paymentMethod->load('membership.member');

        $attributes =  collect($data)
            ->only([
                'bank_name',
                'transit_number',
                'institution_number',
                'account_number',
            ])
            ->filter()
            ->merge([
                'email'   => $paymentMethod->membership->member->email,
                'name'    => "{$paymentMethod->membership->member->first_name} {$paymentMethod->membership->member->last_name}",
                'address' => [
                    'address_1'     => $paymentMethod->membership->member->address,
                    'city'          => $paymentMethod->membership->member->city,
                    'province_code' => 'QC',
                    'postal_code'   => $paymentMethod->membership->member->postal_code,
                ]
            ])
            ->toArray();

        Log::info("[ROTESSA] updating customer (Member #{$paymentMethod->membership->member->id})", $data);

        $response = $this->httpRequest
            ->patch("customers/$paymentMethod->gateway_identifier", $attributes)
            ->throw();

        Log::info("[ROTESSA] updated customer (Member #{$paymentMethod->membership->member->id})", $response->collect()->toArray());

        return $this->setFormatter(new CustomerToPaymentMethod())->format($response);
    }

    public function getCustomer(Membership $membership, $formatter = CustomerToPaymentMethod::class)
    {
        $paymentMethod = $membership->paymentMethods()
            ->where('gateway', $this->getName())
            ->firstOrFail();

        $response = $this->httpRequest->get("customers/$paymentMethod->gateway_identifier");

        return $this->setFormatter(new $formatter())->format($response);
    }

    public function getCustomerByCustomIdentifier($customIdentifier, $formatter = CustomerToPaymentMethod::class)
    {
        $response = $this->httpRequest->post(
            "customers/show_with_custom_identifier",
            ['custom_identifier' => $customIdentifier]
        );

        return $this->setFormatter(new $formatter())->format($response);
    }

    public function getCustomers($query = null, $formatter = CustomerToPaymentMethod::class)
    {
        $response = $this->httpRequest->get('customers', $query);

        $this->setFormatter(new $formatter());

        return $response->collect()->map(fn($customer) => $this->format($customer));
    }

    public function getCustomerSchedules(PaymentMethod $paymentMethod, $query = null)
    {
        $response = $this->httpRequest->get("customers/$paymentMethod->gateway_identifier", $query);

        $this->setFormatter(new ScheduleToSubscription);

        return collect($response->collect()
            ->get('transaction_schedules'))
            ->map(fn($schedule) => $this->format($schedule));
    }

    public function createSchedule(PaymentMethod $paymentMethod, array $data): array
    {
        $response = $this->httpRequest->post('transaction_schedules', [
            'customer_id' => $paymentMethod->gateway_identifier,
            'amount' => $data['amount'],
            'frequency' => Frequencies::$toRotessaFrequencies[$data['frequency']],
            'installments' => $data['installments'],
            'process_date' => $data['start_date'],
            'comment' => $data['comment']
        ])->throw();

        return $this->setFormatter(new ScheduleToSubscription)->format($response);
    }

//    public function getCustomerTransactions(Membership $membership, $query = null)
//    {
//        $paymentMethod = $membership->paymentMethods()->where('gateway', $this->getName())->first();
//
//        if (!$paymentMethod) {
//            Log::warning("[ROTESSA] Membership $membership->id is not connected to Rotessa");
//            return;
//        }
//
//        $response = $this->httpRequest->get("customers/$paymentMethod->gateway_identifier", $query);
//
//        collect($response->collect()->get('financial_transactions'))
//            ->each(function ($responseTransaction) use ($membership) {
//                $transaction = $membership->transactions()
//                    ->where('gateway', $this->getName())
//                    ->where('gateway_identifier', $responseTransaction['id'])
//                    ->first();
//
//                if ($transaction && $this->transactionStatuses[$responseTransaction['status']] == $transaction->status) {
//                    return; // no status updates
//                }
//
//                $subscription = $membership->subscriptions()
//                    ->where('gateway', $this->getName())
//                    ->where('gateway_identifier', $responseTransaction['transaction_schedule_id'])
//                    ->first();
//
//                if (!$subscription) {
//                    Log::warning("[ROTESSA] No subscription for Rotessa Schedule {$responseTransaction['transaction_schedule_id']}");
//                    return;
//                }
//
//                switch ($this->transactionStatuses[$responseTransaction['status']]) {
//                    case Transaction::STATUS_SUCCESS:
//                        Transaction::splitBaseTransaction([], $transaction);
//                        break;
//                    case Transaction::STATUS_PENDING:
//                        Transaction::createPendingTransaction([], $transaction);
//                        break;
//                    case Transaction::STATUS_FAIL:
//                        Transaction::failTransaction([], $transaction);
//                        break;
//                }
//            });
//    }
}
