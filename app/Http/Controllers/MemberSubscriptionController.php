<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Member;
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
        if ($request->gateway != \App\Gateways\Factory::MANUAL) {

            if (!$paymentMethod = $member->paymentMethods()->firstWhere('gateway', $request->gateway)) {
                throw ValidationException::withMessages(['gateway' => 'Member does not have a payment method set up with this gateway']);
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
        }

        $member->subscriptions()->create($request->all());

        return back()->snackbar('Subscription created successfully');
    }
}
