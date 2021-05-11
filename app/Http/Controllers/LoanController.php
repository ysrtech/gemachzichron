<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanRequest;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index()
    {
        return Inertia::render('Loans/Index', [
            'loans' => Loan::with([
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

        if (!$member->membership) {
            return back()->withPartial('AlertModal', [
                'icon'    => 'error',
                'title'   => 'Cannot access loans',
                'message' => "No membership found for $member->first_name $member->last_name"
            ]);
        }

        return Inertia::render('Members/Loans/Index', [
            'member' => $member->only(['id', 'first_name', 'last_name', 'deleted_at', 'membership', 'dependents'])
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
            'loan' => $loan->load('membership.member:id,first_name,last_name', 'dependent:id,name')
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
