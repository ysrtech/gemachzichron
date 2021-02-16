<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ?? '-created_at';
        $status = $request->all('status');

        $invoices = Invoice::filter($status)
            ->searchByRelated($request->search, ['subscription.membership.member'])
            ->with('subscription.membership.member')
            ->when(
                $request->membership_id,
                fn($query) => $query->whereHas('subscription', fn($q) => $q->where('membership_id', $request->membership_id))
            )
            ->when(
                $request->subscription_id,
                fn($query) => $query->where('subscription_id', $request->subscription_id)
            )
            ->orderBy(
                Str::after($sort, '-'),
                Str::startsWith($sort, '-') ? 'desc' : 'asc'
            );

        return Inertia::render('Invoices/Index', [
            'filters' => $request->all('status', 'sort', 'membership_id', 'subscription_id'),
            'invoices' => $invoices->paginate()
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'transactions',
            'subscription.membership.member',
            'payment_method'
        ]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    public function destroy(Invoice $invoice)
    {
        //
    }
}
