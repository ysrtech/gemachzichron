<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
