<?php

namespace App\Models;

use App\Gateways\Rotessa\Formatters\RotessaTransactionToBaseTransaction;
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

    public function convertOrphanedRotessaTransactionsToTransaction()
    {
        Transaction::create((new RotessaTransactionToBaseTransaction)->formatOutput($this->data));
        $this->delete();
    }
}
