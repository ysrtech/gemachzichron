<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Subscriptions/Index', [
            'filters' => $request->all('search', 'amount', 'type', 'active', 'gateway'),
            'subscriptions' => Subscription::searchByRelated($request->search, ['member'])
                ->filter($request->only('amount', 'type', 'active', 'gateway'))
                ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
                ->orderBy('start_date', 'desc')
                ->paginate()
        ]);
    }

    public function show(Subscription $subscription)
    {
        //
    }

    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    public function destroy(Subscription $subscription)
    {
        //
    }
}
