<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDependentRequest;
use App\Models\Member;
use Inertia\Inertia;

class MemberDependentController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Dependents/Index', [
            'member' => $member->load(['dependents' => fn($q) => $q->orderBy('dob')])
        ]);
    }

    public function store(CreateDependentRequest $request, Member $member)
    {
        $member->dependents()->create($request->validated());

        return back()->snackbar('Child added.');
    }
}
