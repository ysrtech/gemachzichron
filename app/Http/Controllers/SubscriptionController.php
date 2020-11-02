<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Membership;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(UpdateSubscriptionRequest $request, Membership $membership)
    {
        $membership->subscriptions()->create($request->validated());

        return back()->with('flash', ['success' => 'Subscription created.']);
    }

    public function show(Subscription $subscription)
    {
        //
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $subscription->update($request->validated());

        return back()->with('flash', ['success' => 'Subscription updated.']);
    }

    public function destroy(Subscription $subscription)
    {
        //
    }
}
