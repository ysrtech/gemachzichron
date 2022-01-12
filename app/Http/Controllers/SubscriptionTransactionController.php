<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManualTransactionRequest;
use App\Models\Subscription;

class SubscriptionTransactionController extends Controller
{
    public function store(CreateManualTransactionRequest $request, Subscription $subscription)
    {
        $subscription->transactions()->create($request->validated())->split();

        return back()->snackbar('Transaction created successfully.');
    }
}
