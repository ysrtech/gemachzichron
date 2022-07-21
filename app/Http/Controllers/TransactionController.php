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
               'gateway', 'gateway_identifier', 'from_date', 'to_date'
           ]),
           'transactions' => Transaction::searchByRelated($request->search, ['member'])
               ->filterByRelated($request->only('loan_id'), 'subscription')
               ->filter($request->only('amount', 'subscription_id', 'status', 'type', 'gateway', 'gateway_identifier'))
               ->filterBetweenDates('process_date', $request->from_date, $request->to_date)
               ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
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
