<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Services\FileExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Members/Index', [
            'filters' => $request->all('search', 'archived'),
            'members' => Member::search($request->search)
                ->filterWithTrashed($request->archived)
                ->select([
                    'id',
                    'first_name',
                    'last_name',
                    'hebrew_name',
                    'email',
                    'home_phone',
                    'mobile_phone',
                    'deleted_at'
                ])
                ->with('membership')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate()
                ->withQueryString()
        ]);
    }

    public function store(UpdateMemberRequest $request)
    {
        Member::create($request->validated());

        return back()->with('flash', ['success' => 'Member created.']);
    }

    public function show(Member $member)
    {
        $member->load([
            'dependents',
            'membership.plan_type',
            'membership.subscriptions',
            'membership.loans.endorsements',
        ]);

        $member->loadEndorsementsToMembers();

        return Inertia::render('Members/Show', [
           'member' => $member
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return back()->with('flash', ['success' => 'Member updated.']);
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return back()->with('flash', ['success' => 'Member archived.']);
    }

    public function restore(Member $member)
    {
        $member->restore();

        return back()->with('flash', ['success' => 'Member restored.']);
    }

    public function export()
    {
        $columns = [
            'first_name',
            'last_name',
            'hebrew_name',
            'wife_name',
            'email',
            'home_phone',
            'mobile_phone',
            'shtibel',
        ];

        $members = Member::select($columns)
            ->get()
            ->toArray();

        return FileExportService::exportCsv(
            $members,
            'members.csv',
            $columns
        );
    }
}
