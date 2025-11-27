<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Transactions/Index', [
           'filters' => $request->all([
               'search', 'amount', 'subscription_id', 'status', 'type', 'loan_id',
               'gateway', 'gateway_identifier', 'from_date', 'to_date', 'sort', 'hide_resolved'
           ]),
           'transactions' => Transaction::searchByRelated($request->search, ['member'])
               ->filterByRelated($request->only('loan_id'), 'subscription')
               ->filter($request->only('amount', 'subscription_id', 'status', 'type', 'gateway', 'gateway_identifier'))
               ->when($request->status == 3 && $request->boolean('hide_resolved'), fn($q) => $q->where('resolved', false))
               ->filterBetweenDates('process_date', $request->from_date, $request->to_date)
               ->with([
                   'member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed(),
                   'subscription:id,type',
                   'resolvingSubscriptions:id,resolves_transaction'
               ])
               ->sort($request->sort)
               ->orderByDesc('process_date')
               ->orderByDesc('id')
               ->paginate(20)
        ]);
    }

    public function show(Transaction $transaction)
    {
        //
    }

    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    public function resolve(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== Transaction::STATUS_FAIL) {
            return back()->alert([
                'icon'    => 'error',
                'title'   => 'Invalid Transaction',
                'message' => 'Only failed transactions can be marked as resolved.',
            ]);
        }

        if ($transaction->resolved) {
            return back()->alert([
                'icon'    => 'info',
                'title'   => 'Already Resolved',
                'message' => 'This transaction is already marked as resolved.',
            ]);
        }

        if (!$request->boolean('confirm')) {
            return back()->alert([
                'icon'         => 'warning',
                'title'        => 'Mark as Resolved?',
                'message'      => "Are you sure you want to mark transaction <strong>#$transaction->id</strong> as resolved?",
                'actionButton' => [
                    'text'   => 'Mark as Resolved',
                    'color'  => 'purple',
                    'route'  => route('transaction.resolve', ['transaction' => $transaction->id, 'confirm' => true]),
                    'method' => 'patch'
                ],
            ]);
        }

        $transaction->update(['resolved' => true]);

        return back()->snackbar('Transaction marked as resolved.');
    }

    public function destroy(Request $request,Transaction $transaction)
    {
        if ($transaction->gateway != 'Manual' ) {

                return back()->alert([
                    'icon'         => 'error',
                    'title'        => 'Delete Transaction Failed',
                    'message'      => "You cannot delete a transaction.",
                ]);
        }

        if (!$request->boolean('confirm')) {
            return back()->alert([
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to delete transaction <strong>#$transaction->id</strong>?",
                'actionButton' => [
                    'text'   => 'Delete',
                    'color'  => 'danger',
                    'route'  => route('transaction.destroy', ['transaction' => $transaction->id, 'confirm' => true]),
                    'method' => 'delete'
                ],
            ]);
        }
        $transaction->delete();



        return back()->snackbar('Transaction Deleted.');
    }

}
