<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin IdeHelperDependent
 */
class Dependent extends Model
{
    use HasFactory;

    protected $casts = [
        'dob' => 'date:Y-m-d', //'date:M j, Y'
        'is_married' => 'boolean'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
