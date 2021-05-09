<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDependentRequest;
use App\Models\Dependent;
use App\Models\Member;
use Inertia\Inertia;

class DependentController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Dependents/Index', [
            'member' => $member->load(['dependents' => fn($q) => $q->orderBy('dob')]),
        ]);
    }

    public function store(CreateDependentRequest $request, Member $member)
    {
        $member->dependents()->create($request->validated());

        return back()->snackbar('Child added.');
    }

    public function show(Dependent $dependent)
    {
        //
    }

    public function update(CreateDependentRequest $request, Dependent $dependent)
    {
        $dependent->update($request->validated());

        return back()->snackbar('Child updated.');
    }

    public function destroy(Dependent $dependent)
    {
//        if ($dependent->loadCount('loans')->loans_count > 0) {
//            return back()->modal([
//                'icon'    => 'error',
//                'title'   => 'Delete Child Failed',
//                'message' => 'You cannot delete a child with associated loans.',
//            ]);
//        }

        $dependent->delete();

        return back()->snackbar('Child deleted.');
    }
}
