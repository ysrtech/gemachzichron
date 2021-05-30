<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanRequest;
use App\Models\Member;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class MemberLoanController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Loans/Index', [
            'member' => $member
                ->load([
                    'loans' => fn($q) => $q->with('guarantors:id,first_name,last_name')->orderBy('loan_date', 'desc'),
                    'dependents'
                ])
                ->loadSum('loans', 'amount')
                ->append('loan_payments_total')
        ]);
    }

    public function store(CreateLoanRequest $request, Member $member)
    {
        $loan = $member->loans()->create(
            Arr::except($request->validated(), 'guarantors')
        );

        $loan->guarantors()->sync($request->guarantors);

        return back()->snackbar('Loan created.');
    }
}
