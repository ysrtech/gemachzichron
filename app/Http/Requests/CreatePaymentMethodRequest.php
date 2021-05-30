<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentMethodRequest extends FormRequest
{
    public function rules()
    {
        return [
            'gateway' => [
                'required',
                Rule::in(Gateway::all()),
                Rule::unique('payment_methods')->where('member_id', $this->route('member.id')),
            ],
            'bank_name' => 'nullable|string',
            'transit_number' => 'required_if:gateway,Rotessa',
            'institution_number' => 'required_if:gateway,Rotessa',
            'account_number' => 'required_if:gateway,Rotessa',
        ];
    }

    public function messages()
    {
        return [
            'gateway.unique' => 'Member already has a payment method set up with this gateway'
        ];
    }
}
