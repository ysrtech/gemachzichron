<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use App\Gateways\Factory as GatewayFactory;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
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

        if ($this->get('type') != Subscription::TYPE_LOAN_PAYMENT) {
            $this->offsetUnset('loan_id');
        }
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
            'gateway'              => ['required', Rule::when($this->gateway != GatewayFactory::MANUAL, Rule::exists('payment_methods', 'id')->where('member_id', $this->route('member')->id))],
            'amount'               => ['required', 'numeric', 'min:1'],
            'start_date'           => ['required', 'date', Rule::when(!$this->gateway_identifier, 'after:today')],
            'installments'         => ['nullable', 'integer'],
            'frequency'            => ['required', Rule::in(Subscription::$frequencies)],
            'comment'              => ['nullable'],
            'membership_fee'       => ['required', 'numeric'],
            'processing_fee'       => ['required', 'numeric'],
            'decline_fee'          => ['required', 'numeric'],
            'resolves_transaction' => [
                'nullable',
                Rule::exists('transactions', 'id')->where('status', Transaction::STATUS_FAIL)
            ],
            'loan_id'              => [
                'sometimes',
                Rule::when($this->guarantor_payment != true, Rule::exists('loans', 'id')->where('member_id', $this->route('member')->id))
            ],
            'guarantor_payment'    => ['nullable'],
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

    public function messages()
    {
        return [
            'resolves_transaction.exists' => 'We didn\'t find a failed transaction with this id.'
        ];
    }
}
