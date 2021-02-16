<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(CreateSubscriptionRequest $request, Membership $membership)
    {
        $paymentMethod = PaymentMethod::create($request->paymentMethodAttributes());

        $membership->subscriptions()->create(
            array_merge(
                $request->subscriptionAttributes(),
                ['payment_method_id' => $paymentMethod->id]
            )
        );

        return back()->snackbar('Subscription created.');
    }

    public function show(Subscription $subscription)
    {
        //
    }

    public function update(CreateSubscriptionRequest $request, Subscription $subscription)
    {
        if (!empty($request->paymentMethodAttributes())) {
            $paymentMethod = PaymentMethod::create($request->paymentMethodAttributes());
        }

        $subscription->update(array_merge(
            $request->subscriptionAttributes(),
            isset($paymentMethod) ? ['payment_method_id' => $paymentMethod->id] : []
        ));

        return back()->snackbar('Subscription updated.');
    }

    public function destroy(Subscription $subscription)
    {
        //
    }
}
