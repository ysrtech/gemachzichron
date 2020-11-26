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

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeWithTotalPaid($query)
    {
        return $query->addSelect([
//            'total_paid' => Transaction::select(DB::raw('sum(ifnull(debit, 0) - ifnull(credit, 0))'))
//                ->whereColumn('member_id', 'members.id')
        ]);
    }
}
