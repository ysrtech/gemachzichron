<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTransaction
 */
class Transaction extends Model
{
    use HasFactory;

    const MAIN_TRANSACTION = 'Main Transaction';
    const GEMACH_FEE_TRANSACTION = 'Gemach Fee';
    const CC_FEE_TRANSACTION = 'Credit Card Fee';

    const RESULT_FAILED = 0;
    const RESULT_SUCCESS = 1;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function setPaymentMethodAttribute(PaymentMethod $paymentMethod)
    {
        $this->attributes['payment_method'] = "{$paymentMethod->type}  {$paymentMethod->last_four_digits}";
    }
}
