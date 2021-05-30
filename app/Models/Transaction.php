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

    const STATUS_SUCCESS = 1;
    const STATUS_PENDING = 2;
    const STATUS_FAIL = 3;
    const STATUS_RESOLVED = 4; // fail resolved

    const TYPE_BASE_TRANSACTION = 'Base Transaction'; // original full amount
    const TYPE_MAIN_TRANSACTION = 'Main Transaction'; // base amount after splitting
    const TYPE_MEMBERSHIP_FEE = 'Membership Fee';
    const TYPE_PROCESSING_FEE = 'Processing Fee';
    const TYPE_DECLINE_FEE = 'Declined Transaction Fee';

    protected $casts = [
        'data' => 'array'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
