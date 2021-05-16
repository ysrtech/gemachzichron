<?php

namespace App\Models;

use App\Models\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends Model
{
    use HasFactory, Commentable;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_LOAN_PAYMENT = 'Loan Payment';

    const FREQUENCY_ONCE = 'Once';
    const FREQUENCY_MONTHLY = 'Monthly';
    const FREQUENCY_BIMONTHLY = 'Bi-Monthly';
    const FREQUENCY_YEARLY = 'Yearly';

    protected $casts = [
        'active' => 'boolean'
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
