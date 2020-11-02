<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMembershipRequest;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        //
    }

    public function store(UpdateMembershipRequest $request, Member $member)
    {
        $member->membership()->create($request->validated());

        return back()->with('flash', ['success' => 'Membership created.']);
    }

    public function show(Membership $membership)
    {
        //
    }

    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        $membership->update($request->validated());

        return back()->with('flash', ['success' => 'Membership updated.']);
    }

    public function destroy(Membership $membership)
    {
        //
    }
}
