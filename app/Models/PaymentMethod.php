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

    const TYPE_CREDIT_CARD = 'Credit Card';
    const TYPE_PAD = 'Pre-authorized Debit';
    const TYPE_CHEQUE = 'Cheque';

    protected $appends = [
        'last_four_digits'
    ];

    protected $visible = [
        'id',
        'type',
        'last_four_digits',
        'cc_expiration'
    ];

    protected $casts = [
        'cc_number'      => 'encrypted',
        'account_number' => 'encrypted',
        'cc_expiration' => 'datetime:m/Y',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getLastFourDigitsAttribute()
    {
        return substr($this->cc_number ?? $this->account_number ?? '', -4);
    }
}
