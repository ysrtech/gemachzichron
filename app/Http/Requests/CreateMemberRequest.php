<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name'        => 'required|string',
            'last_name'         => 'required|string',
            'hebrew_name'       => 'required|string',
            'wife_name'         => 'nullable|string',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string',
            'postal_code'       => 'nullable|string',
            'email'             => 'nullable|email',
            'home_phone'        => 'nullable|string',
            'mobile_phone'      => 'nullable|string',
            'wife_mobile_phone' => 'nullable|string',
            'shtibel'           => 'nullable|string',
        ];
    }
}
