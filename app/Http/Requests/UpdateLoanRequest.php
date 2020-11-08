<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'dependent_id'     => 'required|exists:dependents,id',
            'amount'           => 'required|numeric',
            'loan_date'        => 'required|date',
            'cheque_number'    => 'nullable|string',
            'application_copy' => 'nullable|file',
            'endorsements'     => 'array'
        ];
    }
}
