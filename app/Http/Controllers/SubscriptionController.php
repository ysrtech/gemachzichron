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
            'subscription' => $subscription->load('member', 'member.paymentMethods','paysLoan.member'),
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

    public function previewBulkAdjustLoanPayments()
    {
        $results = [];
        
        // Find members with more than one monthly loan payment subscription
        $members = \App\Models\Member::whereHas('subscriptions', function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      });
            }, '>', 1)
            ->with(['subscriptions' => function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      })
                      ->orderBy('created_at', 'asc');
            }])
            ->get();

        foreach ($members as $member) {
            $result = $member->adjustLoanPaymentSubscriptions(true);
            
            if ($result['success']) {
                $subscription = Subscription::find($result['subscription_id']);
                $results[] = [
                    'member_id' => $member->id,
                    'member_name' => $member->first_name . ' ' . $member->last_name,
                    'subscription_id' => $subscription->id,
                    'current_amount' => $subscription->amount,
                    'new_amount' => 350,
                    'gateway' => $subscription->gateway,
                ];
            }
        }

        return response()->json([
            'count' => count($results),
            'adjustments' => $results,
        ]);
    }

    public function executeBulkAdjustLoanPayments()
    {
        $adjusted = 0;
        $errors = [];
        
        // Find members with more than one monthly loan payment subscription
        $members = \App\Models\Member::whereHas('subscriptions', function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      });
            }, '>', 1)
            ->with(['subscriptions' => function ($query) {
                $query->where('type', Subscription::TYPE_LOAN_PAYMENT)
                      ->where('frequency', Subscription::FREQUENCY_MONTHLY)
                      ->whereHas('paysLoan', function ($q) {
                          $q->where('loan_type', 'Wedding');
                      })
                      ->orderBy('created_at', 'asc');
            }])
            ->get();

        foreach ($members as $member) {
            $result = $member->adjustLoanPaymentSubscriptions();
            
            if ($result['success']) {
                $adjusted++;
            } else {
                // Only track actual errors, not info messages
                if (!str_contains($result['message'], 'does not have multiple') && 
                    !str_contains($result['message'], 'already has') &&
                    !str_contains($result['message'], 'No active')) {
                    $errors[] = "Member #{$member->id}: {$result['message']}";
                }
            }
        }

        if ($adjusted > 0) {
            return back()->snackbar("Successfully adjusted {$adjusted} subscription(s)" . (count($errors) > 0 ? " with " . count($errors) . " error(s)" : ""));
        }

        return back()->alert([
            'icon' => 'info',
            'title' => 'No Adjustments Made',
            'message' => count($errors) > 0 ? implode(', ', $errors) : 'No subscriptions qualified for adjustment.',
        ]);
    }
}
