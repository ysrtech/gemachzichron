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
                ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_MEMBERSHIP))
                ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ], 'amount');
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
        if($this->active_membership){

                $startDate = Carbon::parse($this->membership_since)->floorMonth();

                $oldPlanTypes = [10,11,12];

                $currentDate = Carbon::now()->floorMonth();

                if($startDate->isAfter(Carbon::parse('Feb 29, 2024'))){
                    $membershipMonths = $startDate->diffInMonths($currentDate)+1;
                    $supposedtobePaid =  ($membershipMonths * 190);

                }elseif(in_array($this->plan_type_id,$oldPlanTypes)){
                    $membershipMonths = $startDate->diffInMonths($currentDate)+1;
                    $supposedtobePaid =  ($membershipMonths * 120);

                }else{
                    $lastBeforeIncrease = Carbon::parse('June 20, 2024')->floorMonth();
                    $oldMemershipMonths = $startDate->diffInMonths($lastBeforeIncrease)+1;
                    $newMemershipMonths = $lastBeforeIncrease->diffInMonths($currentDate);

                    $supposedtobePaid =  ($oldMemershipMonths * 120) + ($newMemershipMonths * 190);
                }

                $membershipBalance = $supposedtobePaid - $this->membership_payments_total;

                return $membershipBalance;

        }else{
            return 0;
        }

        
    }









}
