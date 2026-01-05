<?php

namespace App\Models;

use App\Exceptions\DataMismatchException;
use App\Facades\Gateway;
use App\Gateways\Factory;
use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use App\Models\PaymentMethod;
use App\Exceptions\CardknoxApiException;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Subscription extends Model
{
    use HasFactory, Filterable, SearchableByRelated, Sortable, LogsActivity;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_LOAN_PAYMENT = 'Loan Payment';
    const TYPE_PIKUDON = 'Pikudon';

    const FREQUENCY_ONCE = 'Once';
    const FREQUENCY_WEEKLY = 'Weekly';
//    const FREQUENCY_BIWEEKLY = 'Bi-Weekly';
    const FREQUENCY_MONTHLY = 'Monthly';
//    const FREQUENCY_BIMONTHLY = 'Bi-Monthly';
//    const FREQUENCY_QUARTERLY = 'Every 3 months';
//    const FREQUENCY_SEMI_ANNUALLY = 'Every 6 months';
    const FREQUENCY_YEARLY = 'Yearly';

    public static array $frequencies = [
         self::FREQUENCY_ONCE,
         self::FREQUENCY_WEEKLY,
//         self::FREQUENCY_BIWEEKLY,
         self::FREQUENCY_MONTHLY,
//         self::FREQUENCY_BIMONTHLY,
//         self::FREQUENCY_QUARTERLY,
//         self::FREQUENCY_SEMI_ANNUALLY,
         self::FREQUENCY_YEARLY,
    ];

    protected $casts = [
        'active' => 'boolean',
        'gateway_data'   => 'array',
        'deleted_from_gateway' => 'boolean',
    ];

    protected $appends = [
        'transaction_total',
        'transactions_count',
        'transactions_sum',
        'has_transactions'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function resolvesTransaction()
    {
        return $this->belongsTo(Transaction::class, 'resolves_transaction');
    }

    public function paysLoan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getTransactionTotalAttribute()
    {
        return $this->amount + $this->membership_fee + $this->processing_fee + $this->decline_fee;
    }

    public function getTransactionsCountAttribute()
    {
        return $this->transactions
        ->whereIn('type', Transaction::TYPE_MAIN_TRANSACTION)
        ->count();
    }

    public function getHasTransactionsAttribute()
    {
        return $this->transactions->count()?:false;
    }

    public function getTransactionsSumAttribute()
    {
        return $this->transactions
        ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ->sum('amount');
    }

    protected function scopeOrderByMemberLastName($query, $direction)
    {
        $query->orderBy(Member::select('last_name')->whereColumn('members.id', 'subscriptions.member_id'), $direction);
    }

    protected function scopeOrderByMemberFirstName($query, $direction)
    {
        $query->orderBy(Member::select('first_name')->whereColumn('members.id', 'subscriptions.member_id'), $direction);
    }

    public function scopeDefaultSortBy($query)
    {
        $query->orderByDesc('start_date');
    }

    public function syncWithGateway()
    {
        Log::info("Syncing with gateway, subscription $this->id");

        if ($this->isDeletedInGateway()) {
            Log::info("Subscription $this->id is deleted from gateway, cannot sync");
            //return;
        }

        try {
            $gSubscription = Gateway::initialize($this->gateway)->getSchedule($this);


        } catch (RequestException $requestException) {
            if ($requestException->getCode() == 404) {
                Log::info("Subscription $this->id not found on gateway. Response error 404");

                $this->setAsDeletedFromGateway();
                $this->setAsInactive();
            }
            return;
        }

        catch (CardknoxApiException $apiException) {

                Log::info("Subscription $this->id not found on gateway. API error.");

                //$this->setAsDeletedFromGateway();
                //$this->setAsInactive();
            return;
        }



        if ($this->isDeletedInGateway()) {
            $this->setAsNotDeletedFromGateway();
            Log::info("Subscription $this->id reverted deleted from gateway");
        }

        if ($gSubscription['active'] && !$this->active) {
            $this->setAsActive();
            Log::info("Subscription $this->id reverted to active");
        }

        $gatewayAmount = $gSubscription['gateway_data']['amount'] ?? /* $gSubscription['gateway_data']['Amount'] ??*/ null;

        if ($gatewayAmount && $gatewayAmount != $this->transaction_total) {
            Log::info("Subscription $this->id amount does not match gateways schedule amount");
            throw new DataMismatchException('Subscription amount does not match gateways schedule amount');
        }
        //this section is to fix old subscriptions that where not connected to payment method, now it should be connected at creation
        if($this->payment_method_id == NULL  && $this->gateway !== 'Manual' && $gSubscription['gateway_customerid'] !== NULL){

            $paymentMethod = PaymentMethod::where('gateway_identifier', (string)$gSubscription['gateway_customerid'])->first();
            
            if(isset($paymentMethod) && $paymentMethod !== null){
                $gSubscription['payment_method_id'] = $paymentMethod['id'];
            }
        }

        $this->update($gSubscription);
    }

    public function syncWithGatewayConflict(GatewayConflict $missingSubscriptionConflict)
    {
        $attributes = Arr::only($missingSubscriptionConflict->data, [
            'start_date',
            'installments',
            'frequency',
            'active',
            'comment',
            'amount',
        ]);

        $attributes['gateway_data'] = Arr::only($missingSubscriptionConflict->data, [
            'deleted'
        ]);

        $this->update($attributes);
    }

    public function pullTransactionsFromGateway()
    {
        Log::info("Pulling transactions from gateway, subscription $this->id");

        if ($this->isDeletedInGateway()) {
            Log::info("Subscription $this->id is deleted from gateway, cannot pull transactions");
            return;
        }

        Gateway::initialize($this->gateway)
            ->getScheduleTransactions($this)
            ->each(function ($gTransaction) {
                $transaction = Transaction::firstOrNew([
                    'gateway' => $gTransaction['gateway'],
                    'gateway_identifier' => $gTransaction['gateway_identifier']
                ]);

                if ($transaction->type && $transaction->type !== Transaction::TYPE_BASE_TRANSACTION) return;

                $transaction->fill($gTransaction)->save();

                $attributes = Arr::only($gTransaction, [
                    'process_date',
                    'status',
                    'comment',
                    'status_message',
                    'gateway_data',
                ]);

                if ($gTransaction['status'] == Transaction::STATUS_SUCCESS) {
                    $transaction->split($attributes);
                }
            });
    }

    public function pullTransactionsFromGatewayConflicts($gateway, $gatewayIdentifier)
    {
        switch ($gateway) {
            case Factory::ROTESSA:
                GatewayConflict::query()
                    ->where('type', GatewayConflict::TYPE_ORPHANED_ROTESSA_TRANSACTION)
                    ->whereJsonContains('data->transaction_schedule_id', $gatewayIdentifier)
                    ->get()
                    ->each(function (GatewayConflict $conflict) {
                        $conflict->convertOrphanedRotessaTransactionsToTransaction();
                    });
                break;
            case Factory::CARDKNOX:
                // todo
                break;
        }
    }

    public function setAsDeletedFromGateway()
    {
            $gatewayData = is_array($this->gateway_data) ? array_merge($this->gateway_data, ['deleted' => true]) : ['deleted' => true];

            $this->update(['gateway_data' => $gatewayData]);
        
    }

    public function setAsInactive()
    {
        $this->update(['active' => 0]);
    }

    public function setAsActive()
    {
        $this->update(['active' => 1]);
    }



    public function setAsNotDeletedFromGateway()
    {
        $this->update([
            'gateway_data' => array_merge($this->gateway_data, ['deleted' => false])
        ]);
    }

    public function resolveAssociatedConflicts()
    {
        $missingSubscription = $this->getAssociatedConflict();

        if (!$missingSubscription) return;

        if ($missingSubscription->data['deleted'] ?? false) {
            $this->syncWithGatewayConflict($missingSubscription);
            $this->pullTransactionsFromGatewayConflicts($missingSubscription->gateway, $missingSubscription->gateway_identifier);
        } else {
            $this->syncWithGateway();
            $this->pullTransactionsFromGateway();
            $this->pullTransactionsFromGatewayConflicts($missingSubscription->gateway, $missingSubscription->gateway_identifier);
        }

        $missingSubscription->delete();
    }

    public function getAssociatedConflict(): ?GatewayConflict
    {
       return GatewayConflict::where('type', GatewayConflict::TYPE_MISSING_SUBSCRIPTION)
            ->where('gateway', $this->gateway)
            ->where('gateway_identifier', $this->gateway_identifier)
            ->first();
    }

    public function isDeletedInGateway(): bool
    {
        return $this->gateway_data['deleted'] ?? false;
    }

    public function scopeWhereIsNotDeletedInGateway($query)
    {
        // we need this hack since system db does not have json support
        $query
            ->where('gateway_data', 'not like', '%"deleted": true%')
            ->where('gateway_data', 'not like', '%"deleted":true%');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['member_id', 'type', 'amount', 'frequency', 'status', 'start_date', 'end_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
