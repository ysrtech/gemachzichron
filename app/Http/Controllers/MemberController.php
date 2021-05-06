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
        $membersQuery = Member::search($request->search)
            ->filterWithTrashed($request->archived)
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($request->wantsJson()) {
            return $membersQuery
                ->when($request->limit, fn($query, $limit) => $query->limit($limit))
                ->get()
                ->toArray();
        }

        return Inertia::render('Members/Index', [
            'filters' => $request->all('search', 'archived'),
            'members' => $membersQuery->paginate($request->limit ?? 15)
        ]);
    }

    public function store(CreateMemberRequest $request)
    {
        Member::create($request->validated());

        return back()->snackbar('Member created.');
    }

    public function update(CreateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return back()->snackbar('Member updated.');
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
