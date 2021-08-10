<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\CreatePaymentMethodRequest;
use App\Models\Member;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MemberPaymentMethodController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/PaymentMethods/Index', [
            'member' => $member->load('paymentMethods')
        ]);
    }

    public function store(CreatePaymentMethodRequest $request, Member $member)
    {
        try {
            $customer = Gateway::initialize($request->gateway)->createCustomer($member, $request->toArray());
        } catch (NotImplementedException $exception) {
            throw ValidationException::withMessages(['gateway' => 'This gateway does not support creating payment methods']);
        }

        $member->paymentMethods()->create($customer);

        return back()->snackbar('Payment method created successfully');
    }
}
