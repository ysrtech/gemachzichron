<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory, SearchableByRelated, Filterable, Sortable;

    const METHOD_CHEQUE = 'Cheque';
    const METHOD_ETRANSFER = 'Etransfer';
    const METHOD_LOAN_PAYOFF = 'Loan Payoff';

    protected $casts = [
        'amount' => 'float'
    ];

    protected $fillable = [
        'member_id',
        'transaction_id',
        'amount',
        'withdrawal_date',
        'method',
        'comment',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    protected function scopeOrderByMemberLastName($query, $direction)
    {
        $query->orderBy(Member::select('last_name')->whereColumn('members.id', 'withdrawals.member_id'), $direction);
    }

    protected function scopeOrderByMemberFirstName($query, $direction)
    {
        $query->orderBy(Member::select('first_name')->whereColumn('members.id', 'withdrawals.member_id'), $direction);
    }

    public static function getMethods()
    {
        return [
            self::METHOD_CHEQUE,
            self::METHOD_ETRANSFER,
            self::METHOD_LOAN_PAYOFF,
        ];
    }
}
