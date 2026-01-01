<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWithdrawalRequest;
use App\Models\Member;
use Inertia\Inertia;

class MemberWithdrawalController extends Controller
{
    public function index(Member $member)
    {
        $member->load([
            'loans' => fn($q) => $q->with('guarantors:id,first_name,last_name')
                ->withSum('transactions', 'amount')
                ->orderBy('loan_date', 'desc'),
            'withdrawals' => fn($q) => $q->orderBy('withdrawal_date', 'desc'),
            'dependents:member_id,id,name'
        ]);

        $member->loans->each->append('remaining_balance');

        return Inertia::render('Members/Loans/Index', [
            'member' => $member
        ]);
    }

    public function store(CreateWithdrawalRequest $request, Member $member)
    {
        $withdrawal = $member->withdrawals()->create($request->validated());

        // Create a transaction for the withdrawal (OUT direction)
        $transaction = $member->transactions()->create([
            'subscription_id' => null, // Withdrawals don't have subscriptions
            'gateway' => 'Manual',
            'gateway_identifier' => 'withdrawal_' . $withdrawal->id,
            'type' => 'Withdrawal',
            'direction' => 'out',
            'amount' => -$withdrawal->amount, // Negative amount for withdrawal
            'status' => 1, // Success
            'status_message' => 'Withdrawal',
            'process_date' => $withdrawal->withdrawal_date,
            'comment' => 'Withdrawal #' . $withdrawal->id . ($withdrawal->comment ? ': ' . $withdrawal->comment : ''),
        ]);

        // Link the transaction to the withdrawal
        $withdrawal->update(['transaction_id' => $transaction->id]);

        // Log withdrawal transaction creation
        activity()
            ->performedOn($transaction)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $transaction->toArray()])
            ->tap(function($activity) use ($member) {
                $activity->member_id = $member->id;
            })
            ->log('Withdrawal transaction created');

        return back()->snackbar('Withdrawal created.');
    }
}
