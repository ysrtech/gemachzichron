<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use App\Models\Membership;

class LoanController extends Controller
{
    public function index()
    {
        //
    }

    public function store(UpdateLoanRequest $request, Membership $membership)
    {
        $membership->loans()->create($request->validated());

        return back()->with('flash', ['success' => 'Loan created.']);
    }

    public function show(Loan $loan)
    {
        //
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $loan->update($request->validated());

        return back()->with('flash', ['success' => 'Loan updated.']);
    }

    public function destroy(Loan $loan)
    {
        //
    }
}
