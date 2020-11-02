<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'hebrew_name'  => 'required|string',
            'wife_name'    => 'nullable|string',
            'email'        => 'nullable|email',
            'home_phone'   => 'nullable|string',
            'mobile_phone' => 'nullable|string',
            'shtibel'      => 'nullable|string',
        ];
    }
}
