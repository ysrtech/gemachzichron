<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDependentRequest;
use App\Models\Dependent;
use App\Models\Member;

class DependentController extends Controller
{
    public function index(Member $member)
    {
        //
    }

    public function store(UpdateDependentRequest $request, Member $member)
    {
        $member->dependents()->create($request->validated());

        return back()->with('flash', ['success' => 'Dependent added.']);
    }

    public function show(Dependent $dependent)
    {
        //
    }

    public function update(UpdateDependentRequest $request, Dependent $dependent)
    {
        $dependent->update($request->validated());

        return back()->with('flash', ['success' => 'Dependent updated.']);
    }

    public function destroy(Dependent $dependent)
    {
        $dependent->delete();

        return back()->with('flash', ['success' => 'Dependent deleted.']);
    }
}
