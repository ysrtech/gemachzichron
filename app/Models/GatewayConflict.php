<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayConflict extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array'
    ];

    const TYPE_MISSING_MEMBER = 'Missing Member';
    const TYPE_MISSING_SUBSCRIPTION = 'Missing Subscription';
    const TYPE_ORPHANED_ROTESSA_TRANSACTION = 'Orphaned Rotessa Transaction';

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
