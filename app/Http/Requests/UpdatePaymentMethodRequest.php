<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function rules()
    {
        return [
            'gateway' => [
                'required',
                "regex:/{$this->route('paymentMethod.gateway')}/"
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
            'gateway.regex' => 'You cannot update the gateway, you can create a new payment method with another gateway'
        ];
    }
}
