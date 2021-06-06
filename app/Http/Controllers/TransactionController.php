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
               'search', 'amount', 'subscription_id', 'status', 'type',
               'gateway', 'gateway_identifier', 'from_date', 'to_date'
           ]),
           'transactions' => Transaction::searchByRelated($request->search, ['member'])
               ->filter($request->only('amount', 'subscription_id', 'status', 'type', 'gateway', 'gateway_identifier'))
               ->filterBetweenDates('process_date', $request->from_date, $request->to_date)
               ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
               ->orderByDesc('process_date')
               ->orderByDesc('id')
               ->paginate()
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

    public function destroy(Transaction $transaction)
    {
        //
    }
}
