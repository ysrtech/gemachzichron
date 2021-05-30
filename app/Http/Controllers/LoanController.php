<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanRequest;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Loans/Index', [
            'filters' => $request->all('search', 'amount', 'from_date', 'to_date'),
            'loans' => Loan::searchByRelated($request->search, ['membership.member'])
                ->filter($request->only('amount'))
                ->filterBetweenDates('loan_date', $request->from_date, $request->to_date)
                ->with([
                    'membership:id,member_id',
                    'membership.member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed(),
                ])
                ->orderBy('loan_date', 'desc')
                ->paginate()
        ]);
    }

    public function indexForMember(Member $member)
    {
        $member->load([
            'membership.loans' => fn($q) => $q->with('guarantors:id,first_name,last_name')->orderBy('loan_date', 'desc'),
            'dependents'
        ]);

        return Inertia::render('Members/Loans/Index', [
            'member' => $member
                ->append('has_membership')
                ->only(['id', 'first_name', 'last_name', 'has_membership', 'deleted_at', 'membership', 'dependents'])
        ]);
    }

    public function store(CreateLoanRequest $request, Membership $membership)
    {
        $loan = $membership->loans()->create(
            Arr::except($request->validated(), 'guarantors')
        );

        $loan->guarantors()->sync($request->guarantors);

        return back()->snackbar('Loan created.');
    }

    public function show(Loan $loan)
    {
        return Inertia::render('Loans/Show', [
            'loan' => $loan->load([
                'membership.member:id,first_name,last_name',
                'membership.member.dependents:id,member_id,name',
                'dependent:id,name',
                'guarantors:id,first_name,last_name',
            ]),
        ]);
    }

    public function update(CreateLoanRequest $request, Loan $loan)
    {
        $loan->update(
            Arr::except($request->validated(), 'guarantors')
        );

        $loan->guarantors()->sync($request->guarantors);

        return back()->snackbar('Loan updated.');
    }

    public function destroy(Loan $loan)
    {
        //
    }
}
