<?php

namespace App\Http\Controllers\Auth\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Profile\UpdatePasswordRequest;
use App\Http\Requests\Auth\Profile\UpdateProfileRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ProfileController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/Profile/Show');
    }

    public function update(UpdateProfileRequest $request)
    {
        $request->user()->forceFill($request->validated())->save();

        if ($request->email !== $request->user()->email && $request->user() instanceof MustVerifyEmail) {
            $request->user()->update(['email_verified_at' => null]);

            $request->user()->sendEmailVerificationNotification();
        }

        return back()->snackbar('Profile updated successfully.');
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
