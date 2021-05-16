<?php

namespace App\Models;

use App\Models\Traits\Commentable;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMembership
 */
class Membership extends Model
{
    use HasFactory, Filterable, Commentable;

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

    public function scopeWithTotalPaid($query)
    {
        return $query;
    }
}
