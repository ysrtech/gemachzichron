<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Member;
use App\Models\Membership;
use App\Models\PaymentMethod;
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
            'subscriptions' => Subscription::searchByRelated($request->search, ['membership.member'])
                ->filter($request->only('amount', 'type', 'active', 'gateway'))
                ->with([
                    'membership:id,member_id',
                    'membership.member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed(),
                ])
                ->orderBy('start_date', 'desc')
                ->paginate()
        ]);
    }

    public function indexForMember(Member $member)
    {
        $member->load('membership.subscriptions');

        return Inertia::render('Members/Subscriptions/Index', [
            'member' => $member
                ->append('has_membership')
                ->only(['id', 'first_name', 'last_name', 'has_membership', 'deleted_at', 'membership'])
        ]);
    }

    public function store(CreateSubscriptionRequest $request, Membership $membership)
    {
        if ($request->gateway == config('gateways.manual.name')) {
            $membership->subscriptions()->create($request->validated());
        } else {
            /** @var PaymentMethod $paymentMethod */
            $paymentMethod = $membership->paymentMethods()
                ->where('gateway', $request->gateway)
                ->first();

            if (!$paymentMethod) {
                throw ValidationException::withMessages(['gateway' => 'Member does not have a payment method set up with this gateway']);
            }

            try {
                $gatewaySubscription = Gateway::initialize($paymentMethod->gateway)
                    ->createSchedule($paymentMethod, $request->toArray());
            } catch (NotImplementedException $exception) {
                throw ValidationException::withMessages(['gateway' => 'This payment methods gateway does not support creating subscriptions']);
            }

            $membership->subscriptions()
                ->create(array_merge($request->validated(), $gatewaySubscription));
        }

        return back()->snackbar('Subscription created successfully');
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
