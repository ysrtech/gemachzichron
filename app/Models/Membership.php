<?php

namespace App\Models;

use App\Models\Traits\Noteable;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMembership
 */
class Membership extends Model
{
    use HasFactory, Filterable, Noteable;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_PEKUDON = 'Pekudon';

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function booted()
    {
//        static::creating(function(Membership $membership) {
//            $membership->id = $membership->member_id;
//        });

        static::saving(function (Membership $membership) {
            if ($membership->type == Membership::TYPE_PEKUDON) {
                $membership->plan_type_id = null;
            }
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
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

    public function scopeWithTotalPaid(Builder $query)
    {
        return $query->addSelect([
            'total_paid' => Transaction::selectRaw('SUM(amount)')
                ->whereColumn('membership_id', 'memberships.id')
                ->where('type', Transaction::TYPE_MAIN_TRANSACTION)
        ]);
    }
}
