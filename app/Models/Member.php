<?php

namespace App\Models;

use App\Events\MemberUpdated;
use App\Models\Traits\Filterable;
use App\Models\Traits\FilterableWithTrashed;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperMember
 */
class Member extends Model
{
    use HasFactory, SoftDeletes, Searchable, Filterable, FilterableWithTrashed;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_PEKUDON = 'Pekudon';

    protected $casts = [
        'active_membership' => 'boolean'
    ];

    protected array $searchable = [
        'last_name',
        'first_name',
        'hebrew_first_name',
        'hebrew_last_name',
        'email'
    ];

    protected static function booted()
    {
        static::saving(function (Member $member) {
            if ($member->membership_type == Member::TYPE_PEKUDON) {
                $member->plan_type_id = null;
            }
        });

        static::updating(function (Member $member) {
            if (is_null($member->getOriginal('membership_type'))
                && $member->isDirty('membership_type')
            ) {
                $member->membership_since = now();
            }
        });

        static::updated(function (Member $member) {
            MemberUpdated::dispatch($member);
        });
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function planType()
    {
        return $this->belongsTo(PlanType::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function guarantees()
    {
        return $this->belongsToMany(Loan::class, 'guarantors');
    }

    public function scopeWithMembershipPaymentsTotal(Builder $query)
    {
        return $query->addSelect([
            'membership_payments_total' => Transaction::selectRaw('SUM(amount)')
                ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_MEMBERSHIP))
                ->whereColumn('member_id', 'members.id')
                ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ]);
    }

    public function getMembershipPaymentsTotalAttribute()
    {
        if (array_key_exists('membership_payments_total', $this->attributes)) {
            return $this->attributes['membership_payments_total'];
        }

        return $this->transactions()
            ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_MEMBERSHIP))
            ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
            ->sum('amount');
    }

    public function scopeWithLoanPaymentsTotal(Builder $query)
    {
        return $query->addSelect([
            'loan_payments_total' => Transaction::selectRaw('SUM(amount)')
                ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_LOAN_PAYMENT))
                ->whereColumn('member_id', 'members.id')
                ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ]);
    }

    public function getLoanPaymentsTotalAttribute()
    {
        if (array_key_exists('loan_payments_total', $this->attributes)) {
            return $this->attributes['loan_payments_total'];
        }

        return $this->transactions()
            ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_LOAN_PAYMENT))
            ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
            ->sum('amount');
    }
}
