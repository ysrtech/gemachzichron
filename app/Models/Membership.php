<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMembership
 */
class Membership extends Model
{
    use HasFactory, Filterable;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_PEKUDON = 'Pekudon';

    public static function booted()
    {
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

    public function plan_type()
    {
        return $this->belongsTo(PlanType::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Subscription::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeWithTotalPaid($query)
    {
        return $query->withSum(['transactions as total_paid' => function ($q) {
            return $q
                ->where('transactions.type', Transaction::MAIN_TRANSACTION)
                ->where('transactions.result', Transaction::RESULT_SUCCESS);
        }], 'amount');
    }
}
