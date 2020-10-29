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
}
