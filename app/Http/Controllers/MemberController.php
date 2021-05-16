<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::search($request->search)
            ->filterWithTrashed($request->archived)
            ->filterHasRelated($request->only('membership'))
            ->withHasActiveMembership()
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($request->wantsJson()) {
            return $query
                ->when($request->limit, fn($query, $limit) => $query->limit($limit))
                ->get()
                ->toArray();
        }

        return Inertia::render('Members/Index', [
            'filters' => $request->all('search', 'archived', 'membership'),
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
                ->append('has_membership')
                ->load([
                    'membership' => fn($query) => $query
                        ->with('planType', 'comments')
                        ->withTotalPaid()
                ])
        ]);
    }

    public function edit(Member $member)
    {
        return Inertia::render('Members/Edit', [
            'member' => $member
        ]);
    }

    public function update(CreateMemberRequest $request, Member $member)
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
