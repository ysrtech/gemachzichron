<?php

namespace App\Models;

use App\Exceptions\NotImplementedException;
use App\Exceptions\FailedPaymentException;
use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use App\Services\Charge\BankChargeService;
use App\Services\Charge\Chargeable;
use App\Services\Charge\CreditCardChargeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Illuminate\Events\queueable;

/**
 * @mixin IdeHelperInvoice
 */
class Invoice extends Model
{
    use HasFactory, Filterable, SearchableByRelated;

    const DEFAULT_GEMACH_FEE = 5;
    const DEFAULT_CC_FEE_PERCENT = 0.03;

    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;

    const TYPE_AUTO = 'auto';
    const TYPE_MANUAL = 'manual';

    protected $dates = [
        'payment_date'
    ];

    protected $appends = [
        'total'
    ];

    protected static function booted()
    {
        static::created(queueable(function (Invoice $invoice) {
            $invoice->makeTransactions();
        }));
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getTotalAttribute()
    {
        return $this->amount + $this->gemach_fee + $this->cc_processing_fee;
    }

    public function chargeableService(): Chargeable
    {
        switch ($this->payment_method->type) {
            case PaymentMethod::TYPE_CREDIT_CARD:
                return new CreditCardChargeService($this->total);
            case PaymentMethod::TYPE_PAD:
                return new BankChargeService($this->total);
            default:
                throw new NotImplementedException();
        }
    }

    public function makeTransactions()
    {
        try {
            if ($this->chargeableService()->charge()) {
                $this->insertSuccessTransactions();
            }
        } catch (NotImplementedException $exception) {
            //
        } catch (FailedPaymentException $exception) {
            $this->insertFailedTransaction($exception->getMessage());
        }
    }

    public function insertSuccessTransactions()
    {
        $this->transactions()->createMany([
            [
                'subscription_id' => $this->subscription_id,
                'type' => Transaction::MAIN_TRANSACTION,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'result' => Transaction::RESULT_SUCCESS,
                'result_message' => '',
            ],
            [
                'subscription_id' => $this->subscription_id,
                'type' => Transaction::GEMACH_FEE_TRANSACTION,
                'amount' => $this->gemach_fee,
                'payment_method' => $this->payment_method,
                'result' => Transaction::RESULT_SUCCESS,
                'result_message' => '',
            ],
        ]);

        if ($this->cc_processing_fee > 0) {
            $this->transactions()->create([
                'subscription_id' => $this->subscription_id,
                'type' => Transaction::CC_FEE_TRANSACTION,
                'amount' => $this->cc_processing_fee,
                'payment_method' => $this->payment_method,
                'result' => Transaction::RESULT_SUCCESS,
                'result_message' => '',
            ]);
        }

        $this->update([
            'status' => Invoice::STATUS_PAID,
            'payment_date' => now()
        ]);
    }

    public function insertFailedTransaction(string $failMessage)
    {
        $this->transactions()->create([
            'subscription_id' => $this->subscription_id,
            'type' => Transaction::MAIN_TRANSACTION,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'result' => Transaction::RESULT_FAILED,
            'result_message' => $failMessage,
        ]);

        // TODO
        // ^ 'amount' => $this->amount ???? or $this->total
        // should we update $this->payment_method ?
        // should we update $this->cc_processing_fee = 0 ?
    }
}
