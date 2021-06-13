<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConfirmPasswordController extends Controller
{
    public function show(Request $request)
    {
        if ($request->inertia()) {
            return back()->confirmPassword();
        }

        return Inertia::render('Auth/ConfirmPassword');
    }

    public function store(Request $request)
    {
        if (! Auth::validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            return back()->confirmPassword()->withErrors([
                'password' => __('auth.password')
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        if ($request->get('redirect') === false) {
            return back();
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
