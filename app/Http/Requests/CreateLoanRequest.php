<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'dependent_id'     => 'nullable|exists:dependents,id',
            'amount'           => 'required|numeric',
            'loan_date'        => 'required|date',
            'cheque_number'    => 'nullable|string',
            'application_copy' => 'nullable|file',
            'comment'          => 'nullable|string',
            'guarantors'       => 'nullable|array'
        ];
    }
}
