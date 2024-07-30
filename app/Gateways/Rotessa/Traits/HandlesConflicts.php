<?php

namespace App\Gateways\Rotessa\Traits;

use App\Models\GatewayConflict;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Support\GatewayConflictDataStructures\Rotessa as RotessaDataStructure;
use Illuminate\Http\Client\RequestException;

trait HandlesConflicts
{
    public function createMissingSubscriptionFromTransaction($rawTransaction)
    {
        $existingMissingSubscription = GatewayConflict::query()
            ->where('gateway', self::getName())
            ->where('gateway_identifier', $rawTransaction['id'])
            ->first();

        if ($existingMissingSubscription) return;

        $paymentMethod = PaymentMethod::query()
            ->where('gateway', self::getName())
            ->where('gateway_identifier', $rawTransaction['customer_id'])
            ->first();

        if (!$paymentMethod) {
            $this->createMissingMemberFromTransaction($rawTransaction);
            return;
        }

        try {
            $rawSchedule = $this->getRawSchedule($rawTransaction['transaction_schedule_id'])->json();
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                $this->createOrphanedRotessaTransaction($rawTransaction, $paymentMethod);
                $this->createDeletedMissingSubscription($rawTransaction, $paymentMethod);
            }
            throw $exception;
        }


        GatewayConflict::updateOrCreate([
            'member_id'          => $paymentMethod->member_id,
            'payment_method_id'  => $paymentMethod->id,
            'gateway'            => self::getName(),
            'type'               => GatewayConflict::TYPE_MISSING_SUBSCRIPTION,
            'gateway_identifier' => $rawSchedule['id'],
        ],[
            'data' => RotessaDataStructure::missingSubscription($rawSchedule),
        ]);
    }

    public function createOrphanedRotessaTransaction($rawTransaction, PaymentMethod $paymentMethod)
    {

        GatewayConflict::updateOrCreate([
            'member_id'          => $paymentMethod->member_id,
            'payment_method_id'  => $paymentMethod->id,
            'gateway'            => self::getName(),
            'type'               => GatewayConflict::TYPE_ORPHANED_ROTESSA_TRANSACTION,
            'gateway_identifier' => $rawTransaction['id'],
        ], [
            'data'       => is_array($rawTransaction) ? $rawTransaction : $rawTransaction->json()
        ]);
    }

    public function createDeletedMissingSubscription($rawTransaction, $paymentMethod)
    {
        $existingMissingSubscription = GatewayConflict::query()
            ->where('type', GatewayConflict::TYPE_MISSING_SUBSCRIPTION)
            ->where('gateway', self::getName())
            ->where('gateway_identifier', $rawTransaction['transaction_schedule_id'])
            ->where('member_id', $paymentMethod->member_id)
            ->first();

        if ($existingMissingSubscription) {

            $currentData = $existingMissingSubscription->data;
            $currentInstallments = $currentData['installments'] ?? 0;

            $existingMissingSubscription->update([
                'payment_method_id'     => $paymentMethod->id,
                'data' => array_merge($currentData, [
                    'start_date' => min($rawTransaction['start_date'], $currentData['start_date'] ?? 'XXXX-XX-XX'),
                    'installments' => 1 + $currentInstallments,
                    'frequency' => $currentInstallments == 0 ? Subscription::FREQUENCY_ONCE : Subscription::FREQUENCY_MONTHLY,
                    'amount' => $rawTransaction['amount'],
                    'comment' => $rawTransaction['comment'],
                    'active' => false,
                    'deleted' => true,
                ]),
            ]);
        } else {
            GatewayConflict::create([
                'type' => GatewayConflict::TYPE_MISSING_SUBSCRIPTION,
                'gateway' => self::getName(),
                'gateway_identifier' => $rawTransaction['transaction_schedule_id'],
                'payment_method_id'     => $paymentMethod->id,
                'member_id' => $paymentMethod->member_id,
                'data' => [
                    'start_date' => $rawTransaction['process_date'],
                    'installments' => 1,
                    'frequency' => Subscription::FREQUENCY_ONCE,
                    'amount' => $rawTransaction['amount'],
                    'comment' => $rawTransaction['comment'],
                    'active' => false,
                    'deleted' => true,
                ],
            ]);
        }
        return;
    }

    public function createMissingMemberFromTransaction($rawTransaction)
    {
        // todo
    }
}
