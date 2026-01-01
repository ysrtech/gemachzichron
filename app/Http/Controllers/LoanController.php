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
            'filters' => $request->all('search', 'loan_type', 'amount', 'from_date', 'to_date', 'sort'),
            'loans' => Loan::searchByRelated($request->search, ['member'])
                ->filter($request->only('amount', 'loan_type'))
                ->filterBetweenDates('loan_date', $request->from_date, $request->to_date)
                ->sort($request->sort)
                ->with([
                    'member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed(),
                    'dependent:id,name'
                ])
                ->withSum('transactions', 'amount')
                ->orderBy('loan_date', 'desc')
                ->paginate(20)
        ]);
    }

    public function show(Loan $loan)
    {
        return Inertia::render('Loans/Show', [
            'loan' => $loan->load([
                'member' => fn($q) => $q->select(['id', 'first_name', 'last_name'])->withTrashed(),
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

    public function destroy(Request $request, Loan $loan)
    {
        if ($loan->subscriptions->isNotEmpty()) {
            if (!$request->boolean('confirm')) {
                return back()->alert([
                    'icon'         => 'error',
                    'title'        => 'Delete Loan Failed',
                    'message'      => "You cannot delete a loan with associated transaction. Remove loan from subscribtion first.<br>
                              Do you want to delete loan <strong>#$loan->id</strong> from associated subscriptions?",
                    'actionButton' => [
                        'text'   => 'Continue',
                        'color'  => 'danger',
                        'route'  => route('loan.destroy', ['loan' => $loan->id, 'confirm' => true]),
                        'method' => 'delete'
                    ],
                ]);
            }

            $loan->subscriptions->each(fn($subscription) => $subscription->update(['loan_id' => null]));


        }

        if (!$request->boolean('confirm')) {
            return back()->alert([
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to delete loan <strong>#$loan->id</strong>?",
                'actionButton' => [
                    'text'   => 'Delete',
                    'color'  => 'danger',
                    'route'  => route('loan.destroy', ['loan' => $loan->id, 'confirm' => true]),
                    'method' => 'delete'
                ],
            ]);
        }

        // Save loan attributes before deletion
        $loanAttributes = $loan->toArray();
        $loanId = $loan->id;
        $loanType = get_class($loan);

        $loan->guarantors()->detach();
        $member = $loan->member->id;
        $loan->delete();

        // Log the deletion event manually (for permanent delete)
        activity()
            ->performedOn((new $loanType)->forceFill(['id' => $loanId]))
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $loanAttributes])
            ->tap(function($activity) use ($loanAttributes) {
                $activity->member_id = $loanAttributes['member_id'];
            })
            ->log('Loan deleted');


        return redirect()->route('members.loans.index', $member)->snackbar('Loan Deleted.');
    }


}
