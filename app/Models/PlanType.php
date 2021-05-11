<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPlanType
 */
class PlanType extends Model
{
    public $timestamps = false;

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
