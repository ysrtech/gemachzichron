<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends Model
{
    use HasFactory, Filterable, SearchableByRelated;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_LOAN_PAYMENT = 'Loan Payment';

    const FREQUENCY_ONCE = 'Once';
    const FREQUENCY_WEEKLY = 'Weekly';
    const FREQUENCY_BIWEEKLY = 'Bi-Weekly';
    const FREQUENCY_MONTHLY = 'Monthly';
    const FREQUENCY_BIMONTHLY = 'Bi-Monthly';
    const FREQUENCY_QUARTERLY = 'Every 3 months';
    const FREQUENCY_SEMI_ANNUALLY = 'Every 6 months';
    const FREQUENCY_YEARLY = 'Yearly';

    public static array $frequencies = [
         self::FREQUENCY_ONCE,
         self::FREQUENCY_WEEKLY,
         self::FREQUENCY_BIWEEKLY,
         self::FREQUENCY_MONTHLY,
         self::FREQUENCY_BIMONTHLY,
         self::FREQUENCY_QUARTERLY,
         self::FREQUENCY_SEMI_ANNUALLY,
         self::FREQUENCY_YEARLY,
    ];

    protected $casts = [
        'active' => 'boolean',
        'data'   => 'array',
    ];

    protected $appends = [
        'transaction_total'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTransactionTotalAttribute()
    {
        return $this->amount + $this->membership_fee + $this->processing_fee + $this->decline_fee;
    }
}
