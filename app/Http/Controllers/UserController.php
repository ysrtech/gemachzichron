<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when(
            !$request->user()->hasRole(User::ROLE_SUPER_ADMIN),
            fn($query) => $query->where('role', '!=', User::ROLE_SUPER_ADMIN)
        )->paginate();

        return Inertia::render('Users/Index', compact('users'));
    }

    public function store(CreateUserRequest $request)
    {
        User::create($request->validated());

        return back()->with('flash', ['success' => 'User created.']);
    }

    public function show(User $user)
    {
        //
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $attributes = $request->validated();

        if (!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)
            && $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return back()->with('flash', ['error' => 'Cannot update super admin.']);
        }

        if (!$request->password) {
            unset($attributes['password']);
        }

        $user->update($attributes);

        return back()->with('flash', ['success' => 'User updated.']);
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)
            && $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return back()->with('flash', ['error' => 'Cannot delete super admin.']);
        }

        $user->delete();

        return back()->with('flash', ['success' => 'User deleted.']);
    }
}
