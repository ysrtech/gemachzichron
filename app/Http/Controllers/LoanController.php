<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use App\Models\Membership;
use Illuminate\Support\Arr;

class LoanController extends Controller
{
    public function index()
    {
        //
    }

    public function store(UpdateLoanRequest $request, Membership $membership)
    {
        $loan = $membership->loans()->create(
            Arr::except($request->validated(), 'endorsements')
        );

        $loan->endorsements()->sync($request->endorsements);

        return back()->snackbar('Loan created.');
    }

    public function show(Loan $loan)
    {
        //
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $loan->update(
            Arr::except($request->validated(), 'endorsements')
        );

        $loan->endorsements()->sync($request->endorsements);

        return back()->snackbar('Loan updated.');
    }

    public function destroy(Loan $loan)
    {
        //
    }
}
