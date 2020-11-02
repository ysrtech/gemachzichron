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

    const TYPE_MEMBERSHIP = 1;
    const TYPE_PEKUDON = 2;

    protected $guarded = [];

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

    public function getTypeAttribute($type)
    {
        return [
            self::TYPE_MEMBERSHIP => 'Membership',
            self::TYPE_PEKUDON => 'Pekudon'
        ][$type];
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
