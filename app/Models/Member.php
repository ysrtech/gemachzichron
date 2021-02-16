<?php

namespace App\Models;

use App\Models\Traits\FilterableWithTrashed;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperMember
 */
class Member extends Model
{
    use HasFactory, SoftDeletes, Searchable, FilterableWithTrashed;

    protected $appends = [
        'full_name'
    ];

    protected array $searchable = [
        'last_name',
        'first_name',
        'hebrew_name',
        'email'
    ];


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
                'membership' => fn($query2) => $query2->select('id', 'member_id')->with([
                    'member' => fn($query3) => $query3->withTrashed()
                ])
            ])
        ]);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
