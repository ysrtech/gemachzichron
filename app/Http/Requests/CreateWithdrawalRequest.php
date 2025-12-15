<?php

namespace App\Http\Requests;

use App\Models\Withdrawal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateWithdrawalRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount'           => 'required|numeric|min:0.01',
            'withdrawal_date'  => 'required|date',
            'method'           => ['required', Rule::in(Withdrawal::getMethods())],
            'comment'          => 'nullable|string',
        ];
    }
}
