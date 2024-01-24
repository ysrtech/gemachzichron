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
            'filters' => $request->all('search', 'amount', 'type', 'active', 'gateway', 'sort'),
            'subscriptions' => Subscription::searchByRelated($request->search, ['member'])
                ->filter($request->only('amount', 'type', 'active', 'gateway'))
                ->sort($request->sort)
                ->with(['member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()])
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
        if ($subscription->gateway != \App\Gateways\Factory::MANUAL && !$subscription->isDeletedInGateway()) {
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

    public function destroy(Request $request, Subscription $subscription)
    {

        

        if ($subscription->gateway != \App\Gateways\Factory::MANUAL && !$subscription->isDeletedInGateway()) {
            $subscription->syncWithGateway();
            $subscription->pullTransactionsFromGateway();
        }

        if ($subscription->transactions->isNotEmpty()) {
                return back()->alert([
                    'icon'         => 'error',
                    'title'        => 'Delete Subscription Failed',
                    'message'      => "You cannot delete a subscription with existing transactions.",
                    'actionButton' => [
                        'text'   => 'Close',
                        'color'  => 'danger',
                    ],
                ]);
        }

        if (!$request->boolean('confirm')) {
            return back()->alert([
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to delete Subscription <strong>#$subscription->id</strong>?",
                'actionButton' => [
                    'text'   => 'Delete',
                    'color'  => 'danger',
                    'route'  => route('subscription.destroy', ['subscription' => $subscription->id, 'confirm' => true]),
                    'method' => 'post'
                ],
            ]);
        }

        if ($subscription->gateway != \App\Gateways\Factory::MANUAL && !$subscription->isDeletedInGateway()) {
            try {
                Gateway::initialize($subscription->gateway)->removeSchedule($subscription);
            } catch (NotImplementedException $exception) {
                throw ValidationException::withMessages(['gateway' => 'This payment method gateway does not support removing subscriptions']);
                return;
            }
        }


        $subscription->delete();
        $member = $subscription->member->id;

        return redirect()->route('members.subscriptions.index', $member)->snackbar('Subscription Deleted.');
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        

        if ($subscription->gateway != \App\Gateways\Factory::MANUAL && !$subscription->isDeletedInGateway()) {
            $subscription->syncWithGateway();
            $subscription->pullTransactionsFromGateway();
        }

        if (!$request->boolean('confirm')) {

            return back()->alert([
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to cancel Subscription <strong>#$subscription->id</strong>?",
                'actionButton' => [
                    'text'   => 'Cancel',
                    'color'  => 'danger',
                    'route'  => route('subscription.cancel', ['subscription' => $subscription->id, 'confirm' => true]),
                    'method' => 'post'
                ],
            ]);
        }

        if ($subscription->gateway != \App\Gateways\Factory::MANUAL && !$subscription->isDeletedInGateway()) {
            try {
                Gateway::initialize($subscription->gateway)->cancelSchedule($subscription);
            } catch (NotImplementedException $exception) {
                throw ValidationException::withMessages(['gateway' => 'This payment method gateway does not support canceling subscriptions']);
            }
        }else{
            $subscription->setAsInactive();
        }



        $member = $subscription->member->id;

        return redirect()->route('members.subscriptions.index', $member)->snackbar('Subscription Canceled.');
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
