<?php

namespace App\Models;

use App\Events\MemberUpdated;
use App\Models\Traits\Filterable;
use App\Models\Traits\FilterableWithTrashed;
use App\Models\Traits\Searchable;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Member extends Model
{
    use HasFactory, SoftDeletes, Searchable, Filterable, FilterableWithTrashed, Sortable;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_PEKUDON = 'Pekudon';

    protected $casts = [
        'active_membership' => 'boolean'
    ];

    protected $appends = ['membership_due'];

    protected array $searchable = [
        'last_name',
        'first_name',
        'hebrew_first_name',
        'hebrew_last_name',
        'email',
        'id'
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
            if ($member->isDirty('first_name', 'last_name', 'email', 'address', 'city', 'postal_code', 'home_phone', 'cell_phone')) {
                MemberUpdated::dispatch($member);
            }
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

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
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

    public function gatewayConflicts()
    {
        return $this->hasMany(GatewayConflict::class);
    }

    public function scopeWithMembershipPaymentsTotal(Builder $query)
    {
        return $query->withSum([
            'transactions as membership_payments_total' => fn($q) => $q
                ->where('status', Transaction::STATUS_SUCCESS)
                ->where(function($q) {
                    $q->where(function($query) {
                        $query->whereHas('subscription', fn($sub) => $sub->where('type', Subscription::TYPE_MEMBERSHIP))
                              ->where('type', Transaction::TYPE_MAIN_TRANSACTION);
                    })
                    ->orWhere('direction', 'out'); // Include withdrawals (negative amounts)
                })
        ], 'amount');
    }

    public function getMembershipPaymentsTotalAttribute()
    {
        if (array_key_exists('membership_payments_total', $this->attributes)) {
            return $this->attributes['membership_payments_total'];
        }

        return $this->transactions()
            ->where('status', Transaction::STATUS_SUCCESS)
            ->where(function($q) {
                $q->where(function($query) {
                    $query->whereHas('subscription', fn($sub) => $sub->where('type', Subscription::TYPE_MEMBERSHIP))
                          ->where('type', Transaction::TYPE_MAIN_TRANSACTION);
                })
                ->orWhere('direction', 'out'); // Include withdrawals (negative amounts)
            })
            ->sum('amount');
    }

    public function scopeWithLoansCount(Builder $query)
    {

        return $query->withCount(['loans as loans_count' => fn($q) => $q]);
    }

    public function scopeWithLoansTotal(Builder $query)
    {
        return $query->withSum(['loans as loans_total' => fn($q) => $q], 'amount');

    }

    public function scopeWithLoansPayments(Builder $query)
    {

        return $query->withSum([
            'transactions as loans_payments' => fn($q) => $q
                ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_LOAN_PAYMENT))
                ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ], 'amount');

    }

    public function getLoansPaymentsAttribute()
    {
        if (array_key_exists('loans_payments', $this->attributes)) {
            return $this->attributes['loans_payments'];
        }

        return $this->transactions()
            ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_LOAN_PAYMENT))
            ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
            ->sum('amount');
    }

    

    public function getMembershipDueAttribute()
    {
        if($this->active_membership && $this->planType){

            $startDate = Carbon::parse($this->membership_since)->floorMonth();
            $currentDate = Carbon::now()->floorMonth();
            $rates = $this->planType->rates ?? [];

            if (empty($rates)) {
                return 0;
            }

            // Sort rates by date (ascending) to process chronologically
            $sortedRates = collect($rates)->sortKeys()->toArray();
            $supposedtobePaid = 0;

            // If only one rate, simple calculation
            if (count($sortedRates) === 1) {
                $rate = current($sortedRates);
                $months = $startDate->diffInMonths($currentDate) + 1;
                $supposedtobePaid = $months * $rate;
            } else {
                // Multiple rates - process each period
                $ratesArray = array_keys($sortedRates);

                foreach ($ratesArray as $index => $effectiveDate) {
                    $effectiveDateCarbon = Carbon::parse($effectiveDate)->floorMonth();

                    // Only process rates that came into effect on or after membership start
                    if ($effectiveDateCarbon >= $startDate) {
                        // Determine the rate to charge for this period
                        $rate = $sortedRates[$effectiveDate];
                        
                        // Period starts at this rate's effective date
                        $periodStart = $effectiveDateCarbon;
                        
                        // Determine the end of this period (either next rate date or today, whichever is earlier)
                        if ($index < count($ratesArray) - 1) {
                            $nextRateDate = Carbon::parse($ratesArray[$index + 1])->floorMonth();
                            $periodEnd = $nextRateDate->lessThanOrEqualTo($currentDate) ? $nextRateDate->subMonth() : $currentDate;
                        } else {
                            $periodEnd = $currentDate;
                        }

                        // Calculate months in this period
                        $months = $periodStart->diffInMonths($periodEnd) + 1;
                        $supposedtobePaid += $months * $rate;
                    } elseif ($index === count($ratesArray) - 1) {
                        // Special case: this is the last (most recent) rate, and it came into effect BEFORE membership started
                        // Use this rate from membership start to today
                        $rate = $sortedRates[$effectiveDate];
                        $months = $startDate->diffInMonths($currentDate) + 1;
                        $supposedtobePaid += $months * $rate;
                    }
                }
            }

            $membershipBalance = $supposedtobePaid - $this->membership_payments_total;

            return $membershipBalance;

        } else {
            return 0;
        }
    }









}
