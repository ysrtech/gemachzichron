<?php

namespace App\Http\Controllers;

use App\Exceptions\DataMismatchException;
use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Models\Transaction;
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
                ->orderBy('start_date', 'desc')
                ->paginate(20)
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
        $gateway = Gateway::initialize($subscription->gateway);

        $gSubscription = $gateway->getSchedule($subscription);

        $gatewayAmount = $gSubscription['gateway_data']['amount'] ?? $gSubscription['gateway_data']['Amount'] ?? null;

        if ($gatewayAmount && $gatewayAmount != $subscription->transaction_total) {
            throw new DataMismatchException('Subscription amount does not match gateways schedule amount');
        }

        $subscription->update($gSubscription);

        $gateway->getScheduleTransactions($subscription)->each(function ($gTransaction) {
            $transaction = Transaction::firstOrNew([
                'gateway' => $gTransaction['gateway'],
                'gateway_identifier' => $gTransaction['gateway_identifier']
            ]);

            if ($transaction->type !== Transaction::TYPE_BASE_TRANSACTION) return;

            $transaction->update($gTransaction);
        });

        return back();
    }
}
