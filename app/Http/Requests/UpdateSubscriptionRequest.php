<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type'        => 'required|in:'.  Subscription::TYPE_MEMBERSHIP .','. Subscription::TYPE_LOAN_PAYMENT,
            'amount'      => 'required|numeric',
            'start_date'  => 'required|date',
            'recurrences' => 'required|numeric',
            'frequency'   => 'required|in:'.  Subscription::FREQUENCY_MONTHLY .','. Subscription::FREQUENCY_BIMONTHLY,
            'process_day' => 'required|integer|max:31',
        ];
    }
}
