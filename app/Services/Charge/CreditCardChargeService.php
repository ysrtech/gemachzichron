<?php


namespace App\Services\Charge;


use App\Exceptions\FailedPaymentException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CreditCardChargeService extends ChargeService
{
    public function charge(): bool
    {
//        try {
////            Http::post('', ['amount' => $this->amount]);
//        } catch (ConnectionException $exception) {
//            // queue retry?
//        } catch (RequestException $exception) {
//            throw new FailedPaymentException($exception->getMessage());
//        }
//
//        return true;

        $return = rand(0, 5);

        try {
            switch ($return) {
                case 0:
                    return false;
                case 1:
                    throw new ConnectionException('Could not connect to server');
                case 2:
                    throw new RequestException(new Response(new \GuzzleHttp\Psr7\Response(418, [], 'Invalid Credit Card number!')));
            }
        } catch (\Exception $exception) {
            throw new FailedPaymentException($exception->getMessage());
        }

        return true;
    }
}
