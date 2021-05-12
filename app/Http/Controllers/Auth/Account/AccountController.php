<?php

namespace App\Http\Controllers\Auth\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Account\UpdatePasswordRequest;
use App\Http\Requests\Auth\Account\UpdateAccountRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AccountController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/Account/Show');
    }

    public function update(UpdateAccountRequest $request)
    {
        $request->user()->forceFill($request->validated())->save();

        if ($request->email !== $request->user()->email && $request->user() instanceof MustVerifyEmail) {
            $request->user()->update(['email_verified_at' => null]);

            $request->user()->sendEmailVerificationNotification();
        }

        return back()->snackbar('Account updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->forceFill([
            'password' => $request->password,
        ])->save();

        return back()->snackbar('Password updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->user()->fresh()->delete();

        Auth::logout();

        return redirect('/');
    }
}
