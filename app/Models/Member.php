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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Member extends Model
{
    use HasFactory, SoftDeletes, Searchable, Filterable, FilterableWithTrashed, Sortable, LogsActivity;

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

    public function notes()
    {
        return $this->hasMany(Note::class);
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
                
                // Find the rate that was active when membership started
                $activeRateAtStart = null;
                $activeRateStartIndex = 0;
                
                foreach ($ratesArray as $index => $effectiveDate) {
                    $effectiveDateCarbon = Carbon::parse($effectiveDate)->floorMonth();
                    if ($effectiveDateCarbon <= $startDate) {
                        $activeRateAtStart = $sortedRates[$effectiveDate];
                        $activeRateStartIndex = $index;
                    } else {
                        break; // Stop once we find a rate that comes after membership start
                    }
                }

                // Process from membership start with the active rate at that time
                $periodStart = $startDate;
                
                // Start from the rate that was active when membership began
                for ($i = $activeRateStartIndex; $i < count($ratesArray); $i++) {
                    $effectiveDate = $ratesArray[$i];
                    $effectiveDateCarbon = Carbon::parse($effectiveDate)->floorMonth();
                    $rate = $sortedRates[$effectiveDate];
                    
                    // Determine period end
                    if ($i < count($ratesArray) - 1) {
                        // There's a next rate - use the month before it becomes effective
                        $nextRateDate = Carbon::parse($ratesArray[$i + 1])->floorMonth();
                        $periodEnd = $nextRateDate->lessThanOrEqualTo($currentDate) ? $nextRateDate->copy()->subMonth() : $currentDate;
                    } else {
                        // This is the last rate - use current date
                        $periodEnd = $currentDate;
                    }
                    
                    // Only calculate if period is valid
                    if ($periodStart <= $periodEnd) {
                        $months = $periodStart->diffInMonths($periodEnd) + 1;
                        $supposedtobePaid += $months * $rate;
                        
                        // Move to next period (if there is one)
                        if ($i < count($ratesArray) - 1) {
                            $periodStart = Carbon::parse($ratesArray[$i + 1])->floorMonth();
                        }
                    }
                }
            }

            $membershipBalance = $supposedtobePaid - $this->membership_payments_total;

            return $membershipBalance;

        } else {
            return 0;
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'member_id', 'email', 'phone', 'address', 'city', 'state', 'zip', 'active_membership', 'type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Adjust loan payment subscriptions from $250 to $350
     * Finds the oldest active $250 monthly wedding loan subscription and updates it to $350
     * 
     * @param bool $dryRun If true, only validates without making changes
     * @return array ['success' => bool, 'message' => string, 'subscription_id' => int|null]
     */
    public function adjustLoanPaymentSubscriptions($dryRun = false)
    {
        $monthlyLoanSubscriptions = $this->subscriptions()
            ->where('type', Subscription::TYPE_LOAN_PAYMENT)
            ->where('frequency', Subscription::FREQUENCY_MONTHLY)
            ->whereHas('paysLoan', function ($q) {
                $q->where('loan_type', 'Wedding');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Check if member has more than one monthly loan subscription
        if ($monthlyLoanSubscriptions->count() <= 1) {
            return [
                'success' => false,
                'message' => 'Member does not have multiple monthly loan subscriptions.',
                'subscription_id' => null,
            ];
        }

        // Check if at least one is active
        if ($monthlyLoanSubscriptions->where('active', true)->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Member has no active monthly loan subscriptions.',
                'subscription_id' => null,
            ];
        }

        // Check if any subscription has amount of $350 and is active
        if ($monthlyLoanSubscriptions->where('amount', 350)->where('active', true)->isNotEmpty()) {
            return [
                'success' => false,
                'message' => 'Member already has an active $350 subscription.',
                'subscription_id' => null,
            ];
        }

        // Find the oldest active $250 subscription
        $subscriptionToUpdate = $monthlyLoanSubscriptions
            ->where('active', true)
            ->where('amount', 250)
            ->sortBy('created_at')
            ->first();

        if (!$subscriptionToUpdate) {
            return [
                'success' => false,
                'message' => 'No active $250 subscription found to update.',
                'subscription_id' => null,
            ];
        }

        try {
            $oldAmount = $subscriptionToUpdate->amount;
            $newAmount = 350;

            if ($dryRun) {
                return [
                    'success' => true,
                    'message' => "[DRY RUN] Would adjust subscription #{$subscriptionToUpdate->id} from \${$oldAmount} to \${$newAmount}",
                    'subscription_id' => $subscriptionToUpdate->id,
                ];
            }

            $transactionTotal = $newAmount 
                + $subscriptionToUpdate->membership_fee 
                + $subscriptionToUpdate->processing_fee 
                + $subscriptionToUpdate->decline_fee;

            $updateData = [
                'amount' => $newAmount,
                'transaction_total' => $transactionTotal,
                'start_date' => $subscriptionToUpdate->start_date,
                'installments' => $subscriptionToUpdate->installments,
                'membership_fee' => $subscriptionToUpdate->membership_fee,
                'processing_fee' => $subscriptionToUpdate->processing_fee,
                'decline_fee' => $subscriptionToUpdate->decline_fee,
                'comment' => 'Bulk adjustment of loan subscriptions to $350',
            ];

            // Update in gateway if not manual and not deleted from gateway
            if ($subscriptionToUpdate->gateway != \App\Gateways\Factory::MANUAL && !$subscriptionToUpdate->isDeletedInGateway()) {
                try {
                    $gatewayResponse = \App\Facades\Gateway::initialize($subscriptionToUpdate->gateway)->updateSchedule(
                        $subscriptionToUpdate,
                        $updateData
                    );
                    $updateData = array_merge($updateData, $gatewayResponse);
                } catch (\App\Exceptions\NotImplementedException $exception) {
                    \Illuminate\Support\Facades\Log::error("Failed to update subscription #{$subscriptionToUpdate->id} in gateway for member #{$this->id}: Gateway does not support updates");
                    return [
                        'success' => false,
                        'message' => "Gateway {$subscriptionToUpdate->gateway} does not support updating subscriptions.",
                        'subscription_id' => $subscriptionToUpdate->id,
                    ];
                } catch (\Exception $exception) {
                    \Illuminate\Support\Facades\Log::error("Failed to update subscription #{$subscriptionToUpdate->id} in gateway for member #{$this->id}: " . $exception->getMessage());
                    return [
                        'success' => false,
                        'message' => 'Failed to update subscription in gateway: ' . $exception->getMessage(),
                        'subscription_id' => $subscriptionToUpdate->id,
                    ];
                }
            }

            // Update subscription in database (this will trigger activity log automatically)
            $subscriptionToUpdate->update([
                'amount' => $newAmount,
                'membership_fee' => $updateData['membership_fee'],
                'processing_fee' => $updateData['processing_fee'],
                'decline_fee' => $updateData['decline_fee'],
            ]);

            // Log the adjustment
            \Illuminate\Support\Facades\Log::info("Adjusted loan payment subscription #{$subscriptionToUpdate->id} from \${$oldAmount} to \${$newAmount} for member #{$this->id} ({$this->first_name} {$this->last_name})");

            // Send email notification to member
            if ($this->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($this->email)
                        ->send(new \App\Mail\SubscriptionAdjusted($this, $subscriptionToUpdate, $oldAmount, $newAmount));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to send subscription adjustment email to member #{$this->id}: " . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'message' => "Successfully adjusted subscription #{$subscriptionToUpdate->id} from \${$oldAmount} to \${$newAmount}. An email notification has been sent to the member.",
                'subscription_id' => $subscriptionToUpdate->id,
            ];

        } catch (\Exception $exception) {
            \Illuminate\Support\Facades\Log::error("Failed to adjust subscription for member #{$this->id}: " . $exception->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to adjust subscription: ' . $exception->getMessage(),
                'subscription_id' => $subscriptionToUpdate->id ?? null,
            ];
        }
    }
}
