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

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
