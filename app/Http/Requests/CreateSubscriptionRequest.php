<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'membership_fee' => $this->get('membership_fee') ?? 0,
            'processing_fee' => $this->get('processing_fee') ?? 0,
            'decline_fee' => $this->get('decline_fee') ?? 0,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'                 => ['required', Rule::in([Subscription::TYPE_MEMBERSHIP, Subscription::TYPE_LOAN_PAYMENT])],
            'gateway'              => ['required', Rule::in(Gateway::all())],
            'amount'               => ['required', 'numeric', 'min:1'],
            'start_date'           => ['required', 'date', 'after:today'],
            'installments'         => ['nullable', 'integer'],
            'frequency'            => ['required', Rule::in(Subscription::$frequencies)],
            'comment'              => ['nullable'],
            'membership_fee'       => ['required', 'numeric'],
            'processing_fee'       => ['required', 'numeric'],
            'decline_fee'          => ['required', 'numeric'],
            'resolves_transaction' => ['nullable', 'numeric']
        ];
    }

    public function withTransactionTotal()
    {
        return array_merge($this->validated(), [
            'transaction_total' => $this->get('amount')
                + $this->get('membership_fee')
                + $this->get('processing_fee')
                + $this->get('decline_fee')
        ]);
    }
}
