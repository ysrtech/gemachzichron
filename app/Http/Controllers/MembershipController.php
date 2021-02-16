<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMembershipRequest;
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
            'members' =>  Member::search($request->search)
                ->filterWithTrashed($request->archived)
                ->select(['id', 'first_name', 'last_name', 'deleted_at'])
                ->whereHas('membership', fn($query) => $query->filter($request->all('type', 'plan_type_id')))
                ->with(['membership' => fn($query) => $query->filter($request->all('type', 'plan_type_id'))
                    ->withTotalPaid()
                    ->with('plan_type')
                ])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate()
        ]);

    }

    public function store(CreateMembershipRequest $request, Member $member)
    {
        $member->membership()->create($request->validated());

        return back()->snackbar('Membership created.');
    }

    public function show(Membership $membership)
    {
        //
    }

    public function update(CreateMembershipRequest $request, Membership $membership)
    {
        $membership->update($request->validated());

        return back()->snackbar('Membership updated.');
    }

    public function destroy(Membership $membership)
    {
        //
    }
}
