<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMembershipRequest;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Memberships/Index', [
            'filters' => $request->all('search', 'archived'),
            'members' =>  Member::filter($request->all('search', 'archived'))
                ->select(['id', 'first_name', 'last_name'])
                ->whereHas('membership', fn($query) => $query->filter($request->all('type', 'plan_type')))
                ->with(['membership' => function ($query) use ($request) {
                    $query
                        ->filter($request->all('type', 'plan_type'))
                        ->withTotalPaid()
                        ->with('plan_type');
                }])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate()
                ->withQueryString()
        ]);

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
