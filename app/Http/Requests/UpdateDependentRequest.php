<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDependentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name'  => 'required|string',
            'last_name'   => 'required|string',
            'hebrew_name' => 'nullable|string',
            'dob'         => 'required|date_format:Y-m-d'
        ];
    }
}
