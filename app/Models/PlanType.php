<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin IdeHelperPlanType
 */
class PlanType extends Model
{
    public $timestamps = false;

    protected static function booted()
    {
        static::saved(fn($planType) => Cache::forget('plan-types'));
        static::deleted(fn($planType) => Cache::forget('plan-types'));
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
