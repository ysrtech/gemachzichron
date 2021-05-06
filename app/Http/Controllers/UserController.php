<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->paginate();

        return Inertia::render('Users/Index', compact('users'));
    }

    public function store(CreateUserRequest $request)
    {
        User::create($request->validated());

        return back()->snackbar('User created.');
    }

    public function show(User $user)
    {
        //
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $attributes = $request->validated();

        if (!$request->password) unset($attributes['password']);

        $user->update($attributes);

        return back()->snackbar('User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->snackbar('User deleted.');
    }
}
