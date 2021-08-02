<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperLoan
 */
class Loan extends Model
{
    use HasFactory, SearchableByRelated, Filterable;

    protected $casts = [
        'amount' => 'float'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function dependent()
    {
        return $this->belongsTo(Dependent::class);
    }

    public function guarantors()
    {
        return $this->belongsToMany(Member::class, 'guarantors');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Subscription::class)
            ->where('subscriptions.type', Subscription::TYPE_LOAN_PAYMENT)
            ->where('transactions.type', Transaction::TYPE_MAIN_TRANSACTION);
    }

    public function getApplicationCopyAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function setApplicationCopyAttribute(?UploadedFile $file)
    {
        if (!$file) return;

        // delete old application_copy
        if ($this->attributes['application_copy'] ?? false) {
            Storage::delete($this->attributes['application_copy']);
        }

        $this->attributes['application_copy'] = $file->storeAs('applications', "loan_$this->id.{$file->guessExtension()}");
    }

    public function getRemainingBalanceAttribute()
    {
        if (!array_key_exists('transactions_sum_amount', $this->attributes)) {
            // Beware of N+1
            $this->loadSum('transactions', 'amount');
        }

        return $this->amount - $this->attributes['transactions_sum_amount'];
    }
}
