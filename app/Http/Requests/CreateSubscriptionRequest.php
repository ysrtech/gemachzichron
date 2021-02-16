<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type'                              => ['required', 'in:' . Subscription::TYPE_MEMBERSHIP . ',' . Subscription::TYPE_LOAN_PAYMENT],
            'amount'                            => ['required', 'numeric'],
            'start_date'                        => ['required', 'date'],
            'recurrences'                       => ['required', 'numeric'],
            'frequency'                         => ['required', 'in:' . Subscription::FREQUENCY_MONTHLY . ',' . Subscription::FREQUENCY_BIMONTHLY],
            'process_day'                       => ['required', 'integer', 'max:31'],
            'payment_method'                    => ['array'],
            'payment_method.type'               => [
                $this->method() == 'POST' ? 'required' : 'sometimes',
                Rule::in([PaymentMethod::TYPE_CREDIT_CARD, PaymentMethod::TYPE_PAD, PaymentMethod::TYPE_CHEQUE])],
            'payment_method.account_number'     => ['required_if:payment_method.type,'. PaymentMethod::TYPE_PAD],
            'payment_method.transit_number'     => ['required_if:payment_method.type,'. PaymentMethod::TYPE_PAD],
            'payment_method.institution_number' => ['required_if:payment_method.type,'. PaymentMethod::TYPE_PAD],
            'payment_method.cc_number'          => ['required_if:payment_method.type,'. PaymentMethod::TYPE_CREDIT_CARD],
            'payment_method.cc_expiration'      => ['required_if:payment_method.type,'. PaymentMethod::TYPE_CREDIT_CARD, 'nullable', 'date'],
            'payment_method.name_on_card'       => ['required_if:payment_method.type,'. PaymentMethod::TYPE_CREDIT_CARD, 'nullable', 'string'],
        ];
    }

    public function subscriptionAttributes(): array
    {
        return Arr::except($this->validated(), 'payment_method');
    }

    public function paymentMethodAttributes(): array
    {
        return $this->validated()['payment_method'] ?? [];
    }
}
