<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role'     => [
                'nullable',
                'in:' . User::ROLE_ADMIN . (Auth::user()->hasRole(User::ROLE_SUPER_ADMIN) ? ',' . User::ROLE_SUPER_ADMIN : '')
            ],
            'password' => ['nullable', 'string', new Password, 'confirmed'],
        ];
    }
}
