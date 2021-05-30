<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Member;
use App\Models\PaymentMethod;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MemberSubscriptionController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Subscriptions/Index', [
            'member' => $member->load('subscriptions')
        ]);
    }

    public function store(CreateSubscriptionRequest $request, Member $member)
    {
        if ($request->gateway == config('gateways.manual.name')) {
            $member->subscriptions()->create($request->validated());
        } else {
            /** @var PaymentMethod $paymentMethod */
            $paymentMethod = $member->paymentMethods()
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

            $member->subscriptions()
                ->create(array_merge($request->validated(), $gatewaySubscription));
        }

        return back()->snackbar('Subscription created successfully');
    }
}
