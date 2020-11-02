<?php

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateUserRequest extends FormRequest
{
    use PasswordValidationRules;

    public function rules()
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role'     => [
                'nullable',
                'in:' . User::ROLE_ADMIN . (Auth::user()->hasRole(User::ROLE_SUPER_ADMIN) ? ',' . User::ROLE_SUPER_ADMIN : '')
            ],
            'password' => $this->passwordRules(),
        ];
    }
}
