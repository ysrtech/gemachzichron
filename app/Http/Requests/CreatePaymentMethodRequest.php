<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentMethodRequest extends FormRequest
{
    public function rules()
    {
        $rotessa = \App\Gateways\Factory::ROTESSA;
        $cardknox = \App\Gateways\Factory::CARDKNOX;

        return [
            'gateway' => [
                'required',
                Rule::in(Gateway::all()),
                Rule::unique('payment_methods')->where('member_id', $this->route('member.id')),
            ],
            'bank_name' => 'nullable|string',
            'transit_number' => "required_if:gateway,$rotessa",
            'institution_number' => "required_if:gateway,$rotessa",
            'account_number' => "required_if:gateway,$rotessa",
            'card_number' => "required_if:gateway,$cardknox",
            'card_expiry' => ["required_if:gateway,$cardknox", 'nullable', 'date_format:my'],
        ];
    }

    public function messages()
    {
        return [
            'gateway.unique' => 'Member already has a payment method set up with this gateway'
        ];
    }
}
