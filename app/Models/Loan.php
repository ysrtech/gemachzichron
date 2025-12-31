<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\SearchableByRelated;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Loan extends Model
{
    use HasFactory, SearchableByRelated, Filterable, Sortable, LogsActivity;

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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Subscription::class)
            ->where('subscriptions.type', Subscription::TYPE_LOAN_PAYMENT)
            ->where('transactions.type', Transaction::TYPE_MAIN_TRANSACTION);
    }

    protected function scopeOrderByMemberLastName($query, $direction)
    {
        $query->orderBy(Member::select('last_name')->whereColumn('members.id', 'loans.member_id'), $direction);
    }

    protected function scopeOrderByMemberFirstName($query, $direction)
    {
        $query->orderBy(Member::select('first_name')->whereColumn('members.id', 'loans.member_id'), $direction);
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

        $this->attributes['application_copy'] = $file->store('applications');
    }

    public function getRemainingBalanceAttribute()
    {
        if (!array_key_exists('transactions_sum_amount', $this->attributes)) {
            // Beware of N+1
            $this->loadSum('transactions', 'amount');
        }

        return $this->amount - $this->attributes['transactions_sum_amount'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['member_id', 'dependent_id', 'amount', 'loan_type', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
