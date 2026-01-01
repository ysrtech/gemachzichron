<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManualTransactionRequest;
use App\Models\Subscription;

class SubscriptionTransactionController extends Controller
{
    public function store(CreateManualTransactionRequest $request, Subscription $subscription)
    {
        $transaction = $subscription->transactions()->create($request->validated());
        $transaction->split();

        // Log manual transaction creation
        activity()
            ->performedOn($transaction)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $transaction->toArray()])
            ->tap(function($activity) use ($subscription) {
                $activity->member_id = $subscription->member_id;
            })
            ->log('Manual transaction created');

        return back()->snackbar('Transaction created successfully.');
    }
}
