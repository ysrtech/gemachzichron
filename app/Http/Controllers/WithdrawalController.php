<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWithdrawalRequest;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Withdrawals/Index', [
            'filters' => $request->all('search', 'amount', 'from_date', 'to_date', 'sort'),
            'withdrawals' => Withdrawal::searchByRelated($request->search, ['member'])
                ->filter($request->only('amount'))
                ->filterBetweenDates('withdrawal_date', $request->from_date, $request->to_date)
                ->sort($request->sort)
                ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
                ->orderBy('withdrawal_date', 'desc')
                ->paginate(20)
        ]);
    }

    public function show(Withdrawal $withdrawal)
    {
        return Inertia::render('Withdrawals/Show', [
            'withdrawal' => $withdrawal->load([
                'member' => fn($q) => $q->select(['id', 'first_name', 'last_name'])->withTrashed(),
            ]),
        ]);
    }

    public function update(CreateWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        $withdrawal->update($request->validated());

        // Update the associated transaction if it exists
        if ($transaction = $withdrawal->transaction) {
            $transaction->update([
                'amount' => -$withdrawal->amount,
                'process_date' => $withdrawal->withdrawal_date,
                'comment' => 'Withdrawal #' . $withdrawal->id . ($withdrawal->comment ? ': ' . $withdrawal->comment : ''),
            ]);
        }

        return back()->snackbar('Withdrawal updated.');
    }

    public function destroy(Request $request, Withdrawal $withdrawal)
    {
        if (!$request->boolean('confirm')) {
            return back()->alert([
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to delete withdrawal <strong>#$withdrawal->id</strong>?",
                'actionButton' => [
                    'text'   => 'Delete',
                    'color'  => 'danger',
                    'route'  => route('withdrawal.destroy', ['withdrawal' => $withdrawal->id, 'confirm' => true]),
                    'method' => 'delete'
                ],
            ]);
        }

        $member = $withdrawal->member->id;
        
        // Delete the associated transaction if it exists
        if ($transaction = $withdrawal->transaction) {
            $transaction->delete();
        }
        
        $withdrawal->delete();

        return redirect()->route('members.loans.index', $member)->snackbar('Withdrawal Deleted.');
    }
}
