<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\CreatePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\Member;
use App\Models\Membership;
use App\Models\PaymentMethod;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/PaymentMethods/Index', [
            'member' => $member
                ->load('membership.paymentMethods')
                ->append('has_membership')
                ->only(['id', 'first_name', 'last_name', 'has_membership', 'deleted_at', 'membership'])
        ]);
    }

    public function store(CreatePaymentMethodRequest $request, Membership $membership)
    {
        try {
            $customer = Gateway::initialize($request->gateway)
                ->createCustomer($membership, $request->toArray());
        } catch (NotImplementedException $exception) {
            throw ValidationException::withMessages(['gateway' => 'This gateway does not support creating payment methods']);
        }

        $membership->paymentMethods()->create($customer);

        return back()->snackbar('Payment method created successfully');
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        try {
            $customer = Gateway::initialize($request->gateway)
                ->updateCustomer($paymentMethod, $request->toArray());
        } catch (NotImplementedException $exception) {
            throw ValidationException::withMessages(['gateway' => 'This gateway does not support updating payment methods']);
        }

        $paymentMethod->update($customer);

        return back()->snackbar('Payment method updated successfully');
    }
}
