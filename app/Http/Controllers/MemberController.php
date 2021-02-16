<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Models\Member;
use App\Services\ExportService;
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

    public function show(Member $member)
    {
        $member->load(['membership' => fn($query) => $query->withTotalPaid()]);

        $member->loadMissing(
            'dependents',
            'membership.plan_type',
            'membership.subscriptions.payment_method',
            'membership.loans.endorsements',
        );

        $member->loadEndorsementsToMembers();

        $response = Inertia::render('Members/Show', [
           'member' => $member
        ]);

        if ($member->deleted_at) {
            $response->banner(
                'You are viewing an archived member',
                ['level' => 'warning', 'closable' => false]
            );
        }

        return $response;
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

        return ExportService::streamCsv(
            $members,
            'members.csv',
            $columns
        );
    }
}
