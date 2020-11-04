<?php

namespace App\Models;

use App\Models\Traits\RouteBindingWithTrashed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperMember
 */
class Member extends Model
{
    use HasFactory, SoftDeletes, RouteBindingWithTrashed;

    protected $guarded = [];

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function given_endorsements()
    {
        return $this->belongsToMany(Loan::class, 'endorsement_loan');
    }

    public function loadEndorsementsToMembers()
    {
        // Instead of loading all columns by using load('given_endorsements.membership.member')
        return $this->load([
            'given_endorsements' => fn($query1) => $query1->select('membership_id')->with([
                'membership' => fn($query2) => $query2->select('id', 'member_id')->with('member')
            ])
        ]);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('last_name', 'like', "{$search}%")
                    ->orWhere('first_name', 'like', "{$search}%")
                    ->orWhere('hebrew_name', 'like', "{$search}%")
                    ->orWhere('email', 'like', "{$search}%");
            })
            ->when($filters['archived'] ?? null, function ($query, $trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            });
    }
}
