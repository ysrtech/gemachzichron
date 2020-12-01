<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends Model
{
    use HasFactory;

    const TYPE_MEMBERSHIP = 'Membership';
    const TYPE_LOAN_PAYMENT = 'Loan Payment';

    const FREQUENCY_MONTHLY = 'Monthly';
    const FREQUENCY_BIMONTHLY = 'Bi-Monthly';


    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function createInvoice()
    {
        return $this->invoices()->create([
            'payment_method_id' => $this->payment_method_id,
            'amount'     => $this->amount - Invoice::DEFAULT_GEMACH_FEE,
            'gemach_fee' => Invoice::DEFAULT_GEMACH_FEE,
            'cc_processing_fee' => $this->calculateCcProcessingFee()
        ]);
    }

    public function calculateCcProcessingFee()
    {
        return $this->payment_method->type === PaymentMethod::TYPE_CREDIT_CARD
            ? $this->amount * Invoice::DEFAULT_CC_FEE_PERCENT
            : 0;
    }

    public function scopeActive($query)
    {
        // Checks if subscription started and if there are still recurrences available
        return $query
            ->where('start_date', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->select(DB::raw('count(*)'))
                    ->from('invoices')
                    ->whereColumn('subscription_id', 'subscriptions.id');
            }, '<', DB::raw('subscriptions.recurrences'));
    }
}
