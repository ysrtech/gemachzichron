<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::search($request->search)
            ->filterWithTrashed($request->archived)
            ->filterNull($request->only('membership_since'))
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($request->wantsJson()) {
            return $query
                ->when($request->limit, fn($query, $limit) => $query->limit($limit))
                ->get()
                ->toArray();
        }

        return Inertia::render('Members/Index', [
            'filters' => $request->all('search', 'archived', 'membership_since'),
            'members' => $query->paginate($request->limit ?? 15)
        ]);
    }

    public function create()
    {
        return Inertia::render('Members/Edit');
    }

    public function store(CreateMemberRequest $request)
    {
        $member = Member::create($request->validated());

        return redirect()
            ->route('members.show', $member)
            ->snackbar('Member created.');
    }

    public function show(Member $member)
    {
        return Inertia::render('Members/Show', [
            'member' => $member
                ->load('planType')
                ->append('membership_payments_total')
        ]);
    }

    public function edit(Member $member)
    {
        return Inertia::render('Members/Edit', [
            'member' => $member
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return redirect()
            ->route('members.show', $member)
            ->snackbar('Member updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return back()->snackbar('Member archived.');
    }

    public function restore(Member $member)
    {
        $member->restore();

        return back()->snackbar('Member restored.');
    }
}
