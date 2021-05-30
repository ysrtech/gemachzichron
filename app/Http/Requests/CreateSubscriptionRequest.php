<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'           => ['required', Rule::in([Subscription::TYPE_MEMBERSHIP, Subscription::TYPE_LOAN_PAYMENT])],
            'gateway'        => ['required', Rule::in(Gateway::all())],
            'amount'         => ['required', 'numeric', 'min:1'],
            'start_date'     => ['date', 'after_or_equal:today'],
            'installments'   => ['nullable', 'integer'],
            'frequency'      => ['required', Rule::in(Subscription::$frequencies)],
            'comment'        => ['nullable'],
            'membership_fee' => ['required', 'numeric'],
            'processing_fee' => ['nullable', 'numeric'],
            'decline_fee'    => ['nullable', 'numeric'],
            'data'           => ['nullable', 'json']
        ];
    }
}
