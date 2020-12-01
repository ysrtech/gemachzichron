<?php


namespace App\Services\Charge;


use App\Exceptions\FailedPaymentException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class BankChargeService extends ChargeService
{
    public function charge(): bool
    {
        // TODO: Implement charge() method.

        $return = rand(0, 5);

        try {
            switch ($return) {
                case 0:
                    return false;
                case 1:
                    throw new ConnectionException('Could not connect to server');
                case 2:
                    throw new RequestException(new Response(new \GuzzleHttp\Psr7\Response(418, [], 'Invalid account number!')));
            }
        } catch (\Exception $exception) {
            throw new FailedPaymentException($exception->getMessage());
        }

        return true;
    }
}
