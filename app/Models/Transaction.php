<?php

namespace App\Models;

use App\Exceptions\DataMismatchException;
use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTransaction
 */
class Transaction extends Model
{
    use HasFactory, Filterable, SearchableByRelated;

    const STATUS_SUCCESS = 1;
    const STATUS_PENDING = 2;
    const STATUS_FAIL = 3;
    const STATUS_RESOLVED = 4; // fail resolved

    const TYPE_BASE_TRANSACTION = 'Base Transaction'; // original full amount
    const TYPE_MAIN_TRANSACTION = 'Main Transaction'; // amount after splitting
    const TYPE_MEMBERSHIP_FEE = 'Membership Fee';
    const TYPE_PROCESSING_FEE = 'Processing Fee';
    const TYPE_DECLINE_FEE = 'Decline Fees';

    protected $casts = [
        'gateway_data' => 'array'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function isSplit(): bool
    {
        return $this->type != self::TYPE_BASE_TRANSACTION;
    }

    /**
     * Splits base transaction into separate transactions for main and fees
     */
    public function split($attributes = [])
    {
        if ($this->isSplit()) {
            throw new Exception('Transaction is already split');
        }

        if ($this->amount != $this->subscription->transaction_total) {
            throw new DataMismatchException('Base transaction amount does not match subscription total amount');
        }

        $this->update(array_merge([
            'type'   => self::TYPE_MAIN_TRANSACTION,
            'amount' => $this->subscription->amount,
        ], $attributes));

        if ($this->subscription->membership_fee) {
            $this->replicate(['type', 'amount'])->fill([
                'type'   => self::TYPE_MEMBERSHIP_FEE,
                'amount' => $this->subscription->membership_fee,
            ])->save();
        }

        if ($this->subscription->processing_fee) {
            $this->replicate(['type', 'amount'])->fill([
                'type'   => self::TYPE_PROCESSING_FEE,
                'amount' => $this->subscription->processing_fee,
            ])->save();
        }

        if ($this->subscription->decline_fee) {
            $this->replicate(['type', 'amount'])->fill([
                'type'   => self::TYPE_DECLINE_FEE,
                'amount' => $this->subscription->decline_fee,
            ])->save();
        }
    }

    /**
     * merges main/fees transactions into one base transaction
     */
    public function unsplit($attributes = [])
    {
        $transactions = Transaction::where('gateway', $this->gateway)
            ->where('gateway_identifier', $this->gateway_identifier)
            ->get();

        if ($transactions->where('type', self::TYPE_BASE_TRANSACTION)->isNotEmpty()) {
            throw new Exception('Transaction is not split');
        }

        if ($transactions->sum('amount') != $this->subscription->transaction_total) {
            throw new DataMismatchException('Transactions sum does not match subscription total amount');
        }

        $transactions
            ->reject(fn($transaction) => $transaction->is($this))
            ->each->delete();

            $this->update(array_merge([
                'type' => self::TYPE_BASE_TRANSACTION,
                'amount' => $this->subscription->transaction_total
            ], $attributes));
    }
}
