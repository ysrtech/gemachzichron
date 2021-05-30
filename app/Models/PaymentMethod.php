<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin IdeHelperPaymentMethod
 */
class PaymentMethod extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
