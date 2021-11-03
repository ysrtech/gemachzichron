<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
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
            'active'         => ['sometimes', 'boolean'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'installments'   => ['nullable', 'integer'], // cardknox/manual only
            'start_date'     => ['required', 'date', /*'after:today'*/], // cardknox/manual only
            'comment'        => ['nullable'],
            'membership_fee' => ['required', 'numeric'],
            'processing_fee' => ['required', 'numeric'],
            'decline_fee'    => ['required', 'numeric'],
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
