<?php

namespace App\Http\Requests;

use App\Facades\Gateway;
use App\Gateways\Factory;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateManualTransactionRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $subscription = $this->route('subscription');

        $this->merge([
            'member_id' => $subscription->member_id,
            'gateway' => $subscription->gateway,
            'type' => Transaction::TYPE_BASE_TRANSACTION,
            'amount' => $subscription->transaction_total,
            'status' => Transaction::STATUS_SUCCESS,
        ]);
    }

    public function rules()
    {
        return [
            'member_id' => [],
            'gateway' => [Rule::in([Factory::MANUAL])],
            'type' => [],
            'amount' => [],
            'status' => [],
            'process_date' => ['required', 'date'],
            'comment' => [],
        ];
    }
}
