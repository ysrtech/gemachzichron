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
        'dob' => 'date:Y-m-d' //'date:M j, Y'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
