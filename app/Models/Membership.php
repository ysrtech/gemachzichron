<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMembership
 */
class Membership extends Model
{
    use HasFactory, Filterable;

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Subscription::class);
    }

    public function scopeWithTotalPaid($query)
    {
        return $query->withSum(['transactions as total_paid' => fn($q) => $q
            ->where('transactions.type', Transaction::MAIN_TRANSACTION)
            ->where('transactions.result', Transaction::RESULT_SUCCESS)],
            'amount');
    }
}
