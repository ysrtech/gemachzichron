<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        try {
            $customer = Gateway::initialize($request->gateway)->updateCustomer($paymentMethod, $request->toArray());
        } catch (NotImplementedException $exception) {
            throw ValidationException::withMessages(['gateway' => 'This gateway does not support updating payment methods']);
        }

        $paymentMethod->update($customer);

        return back()->snackbar('Payment method updated successfully');
    }
}
