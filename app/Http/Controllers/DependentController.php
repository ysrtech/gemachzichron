<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDependentRequest;
use App\Models\Dependent;
use App\Models\Member;

class DependentController extends Controller
{
    public function index(Member $member)
    {
        //
    }

    public function store(CreateDependentRequest $request, Member $member)
    {
        $member->dependents()->create($request->validated());

        return back()->snackbar('Dependent added.');
    }

    public function show(Dependent $dependent)
    {
        //
    }

    public function update(CreateDependentRequest $request, Dependent $dependent)
    {
        $dependent->update($request->validated());

        return back()->snackbar('Dependent updated.');
    }

    public function destroy(Dependent $dependent)
    {
        if ($dependent->loadCount('loans')->loans_count > 0) {
            return back()->modal([
                'icon'    => 'error',
                'title'   => 'Delete Dependent Failed',
                'message' => 'You cannot delete a dependent that has associated loans.',
            ]);
        }

        $dependent->delete();

        return back()->snackbar('Dependent deleted.');
    }
}
