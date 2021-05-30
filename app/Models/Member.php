<?php

namespace App\Models;

use App\Events\MemberUpdated;
use App\Models\Traits\FilterableByRelated;
use App\Models\Traits\FilterableWithTrashed;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperMember
 */
class Member extends Model
{
    use HasFactory, SoftDeletes, Searchable, FilterableWithTrashed, FilterableByRelated;

    protected array $searchable = [
        'last_name',
        'first_name',
        'hebrew_first_name',
        'hebrew_last_name',
        'email'
    ];

    protected static function booted()
    {
        static::updated(function (Member $member) {
            MemberUpdated::dispatch($member);
        });
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function guarantees()
    {
        return $this->belongsToMany(Loan::class, 'guarantors');
    }

    public function scopeWithHasMembership(Builder $query)
    {
        $query->addSelect([
            'has_membership' => Membership::select('id')
                ->whereColumn('member_id', 'members.id')
                ->limit(1)
        ]);
    }

    public function scopeWithHasActiveMembership(Builder $query)
    {
        $query->addSelect([
            'has_active_membership' => Membership::select('is_active')
                ->whereColumn('member_id', 'members.id')
                ->limit(1)
        ]);
    }

    public function getHasMembershipAttribute($value)
    {
        if (array_key_exists('has_membership', $this->attributes)) {
            return (bool) $value;
        }

        return !is_null($this->loadMissing('membership')->membership);
    }

    public function getHasActiveMembershipAttribute($value)
    {
        if (array_key_exists('has_active_membership', $this->attributes)) {
            return (bool) $value;
        }

        return $this->membership ? $this->membership->is_active : false;
    }
}
