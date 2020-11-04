<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMembership
 */
class Membership extends Model
{
    use HasFactory;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_PEKUDON = 'Pekudon';

    protected $guarded = [];

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

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['type'] ?? null, fn($query, $type) => $query->where('type', $type))
            ->when($filters['plan_type'] ?? null, fn($query, $planType) => $query->where('plan_type_id', $planType));
    }

    public function scopeWithTotalPaid($query)
    {
        return $query->addSelect([
//            'total_paid' => Transaction::select(DB::raw('sum(ifnull(debit, 0) - ifnull(credit, 0))'))
//                ->whereColumn('member_id', 'members.id')
        ]);
    }
}
