<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanRequest;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Loans/Index', [
            'filters' => $request->all('search', 'amount', 'from_date', 'to_date'),
            'loans' => Loan::searchByRelated($request->search, ['member'])
                ->filter($request->only('amount'))
                ->filterBetweenDates('loan_date', $request->from_date, $request->to_date)
                ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
                ->withSum('transactions', 'amount')
                ->orderBy('loan_date', 'desc')
                ->paginate()
        ]);
    }

    public function show(Loan $loan)
    {
        return Inertia::render('Loans/Show', [
            'loan' => $loan->load([
                'member:id,first_name,last_name',
                'member.dependents:id,member_id,name',
                'dependent:id,name',
                'guarantors:id,first_name,last_name',
            ])->append('remaining_balance'),
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
