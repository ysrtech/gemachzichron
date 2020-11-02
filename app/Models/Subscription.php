<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    const TYPE_MEMBERSHIP = 1;
    const TYPE_LOAN_PAYMENT = 2;

    const FREQUENCY_MONTHLY = 1;
    const FREQUENCY_BIMONTHLY = 2;

    protected $guarded = [];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function getTypeAttribute($type)
    {
        return [
            self::TYPE_MEMBERSHIP => 'Membership',
            self::TYPE_LOAN_PAYMENT => 'Loan Payment'
        ][$type];
    }

    public function getFrequencyAttribute($frequency)
    {
        return [
            self::FREQUENCY_MONTHLY => 'Monthly',
            self::FREQUENCY_BIMONTHLY => 'Bi-Monthly'
        ][$frequency];
    }
}
