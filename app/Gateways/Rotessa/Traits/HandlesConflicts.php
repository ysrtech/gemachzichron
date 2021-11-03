<?php

namespace App\Gateways\Rotessa\Traits;

use App\Models\GatewayConflict;
use App\Models\PaymentMethod;
use App\Support\GatewayConflictDataStructures\Rotessa as RotessaDataStructure;
use Illuminate\Http\Client\RequestException;

trait HandlesConflicts
{
    public function createMissingSubscriptionFromTransaction($rawTransaction)
    {
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
                return;
            }
            throw $exception;
        }

        GatewayConflict::updateOrCreate([
            'member_id'          => $paymentMethod->member_id,
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
            'gateway'            => self::getName(),
            'type'               => GatewayConflict::TYPE_ORPHANED_ROTESSA_TRANSACTION,
            'gateway_identifier' => $rawTransaction['id'],
        ], [
            'data'       => is_array($rawTransaction) ? $rawTransaction : $rawTransaction->json()
        ]);
    }

    public function createMissingMemberFromTransaction($rawTransaction)
    {

    }
}
