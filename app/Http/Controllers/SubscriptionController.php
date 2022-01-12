<?php

namespace App\Http\Controllers;

use App\Exceptions\DataMismatchException;
use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Gateways\Factory as GatewayFactory;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\GatewayConflict;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
                ->orderByDesc('start_date')
                ->paginate(20),
            'missing_subscriptions_count' => GatewayConflict::where('type', GatewayConflict::TYPE_MISSING_SUBSCRIPTION)->count()
        ]);
    }

    public function show(Subscription $subscription)
    {
        return Inertia::render('Subscriptions/Show', [
            'subscription' => $subscription->load('member:id,first_name,last_name', 'member.paymentMethods')
        ]);
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        if ($subscription->gateway != \App\Gateways\Factory::MANUAL) {
            try {
                $request->merge(
                    Gateway::initialize($subscription->gateway)->updateSchedule(
                        $subscription,
                        $request->withTransactionTotal()
                    )
                );
            } catch (NotImplementedException $exception) {
                throw ValidationException::withMessages(['gateway' => 'This payment methods gateway does not support updating subscriptions']);
            }
        }

        // todo make sure pending/failed transactions won't be modified when splitting

        $subscription->update($request->all());

        return back()->snackbar('Subscription updated successfully');
    }

    public function destroy(Subscription $subscription)
    {
        //
    }

    public function refresh(Subscription $subscription)
    {
        if ($subscription->gateway === GatewayFactory::MANUAL) return back()->banner('Subscription is not managed by a payment gateway', 'warning');

        try {
            $subscription->syncWithGateway();
        } catch (DataMismatchException $exception) {
            // todo create GatewayConflict
        }

        $subscription->pullTransactionsFromGateway();

        return back();
    }
}
