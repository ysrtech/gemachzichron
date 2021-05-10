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

    protected array $searchable = [
        'last_name',
        'first_name',
        'hebrew_first_name',
        'hebrew_last_name',
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
}
