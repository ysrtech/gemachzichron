<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Gateways\Factory as GatewayFactory;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\GatewayConflict;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MemberSubscriptionController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Subscriptions/Index', [
            'member' => $member->load('subscriptions', 'paymentMethods:id,member_id,gateway')
        ]);
    }

    public function store(CreateSubscriptionRequest $request, Member $member)
    {
        if ($request->gateway == GatewayFactory::MANUAL) {
            $member->subscriptions()->create($request->validated());

            return back()->snackbar('Subscription created successfully');
        }

        if (!$paymentMethod = $member->paymentMethods()->firstWhere('id', $request->gateway)) {
            throw ValidationException::withMessages(['gateway' => 'Member does not have a payment method set up with this gateway']);
        }

        $request->payment_method_id = $paymentMethod->id;

        if ($request->gateway_identifier) { // sync with existing gateway schedule

            $conflict = GatewayConflict::where('type', GatewayConflict::TYPE_MISSING_SUBSCRIPTION)
                ->where('gateway', $paymentMethod->gateway)
                ->where('gateway_identifier', $request->gateway_identifier)
                ->firstOr(function () {
                    throw ValidationException::withMessages(['gateway_identifier' => 'We didn\'t find a missing subscription with this Schedule ID']);
                });

            /**
             * @var Subscription $subscription
             */
            $comment = 'Created from missing subscription';
            $data = array_merge($conflict->data, ['id' => $request->gateway_identifier]);
            $validated = $request->validated();
            $validatedMerged = array_merge($validated, ['gateway' => $paymentMethod->gateway,
                'comment' => $comment, 'payment_method_id' => $paymentMethod->id, 'gateway_data' => $data]);

            $subscription = $member->subscriptions()->create(
                array_merge($validatedMerged, $request->only('gateway_identifier'))
            );

            $subscription->resolveAssociatedConflicts();

            return back()->snackbar('Subscription created and synced successfully');
        }

        

        try {
            $request->merge(
                Gateway::initialize($paymentMethod->gateway)->createSchedule(
                    $paymentMethod,
                    $request->withTransactionTotal()
                )
            );
        } catch (NotImplementedException $exception) {
            throw ValidationException::withMessages(['gateway' => 'This payment methods gateway does not support creating subscriptions']);
        }

        

        $member->subscriptions()->create($request->all());

        if ($request->resolves_transaction) {
            try {
                Transaction::find($request->resolves_transaction)->resolve();
            } catch (\Exception $exception) {
                // todo revert/delete subscription??
            }
        }

        return back()->snackbar('Subscription created successfully');
    }

    
}
