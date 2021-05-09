<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDependentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'       => 'required|string',
            'dob'        => 'required|date_format:Y-m-d',
            'is_married' => 'required|boolean'
        ];
    }
}
