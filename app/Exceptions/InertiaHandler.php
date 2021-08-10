<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\RequestException;
use Inertia\Inertia;
use Throwable;

trait InertiaHandler
{
    protected function prepareInertiaResponse($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            // Put the intended url in the session
            redirect()->guest($e->redirectTo() ?? route('login'));

            return Inertia::flash('login_modal', [], 'Auth/Login', [], 401);
        }

        $response = parent::render($request, $e);

        $messages = [
            403 => $e->getMessage() ?: 'Forbidden',
            404 => 'Page Not Found',
            419 => 'The page expired, please try again',
            429 => 'Too Many Requests',
            500 => $e instanceof RequestException || $e instanceof CardknoxApiException || $e instanceof DataMismatchException
                ? $e->getMessage()
                : 'Whoops, something went wrong on our servers',
            503 => 'We are doing some maintenance, Please check back soon',
        ];

        switch ($response->status()) {
            case 403:
            case 404:
            case 429:
                return Inertia::render('Errors/Show', ['message' => $messages[$response->status()]])
                    ->toResponse($request)
                    ->setStatusCode($response->status());
            case 419:
            case 503:
                return Inertia::flash(
                    'banner', ['message' => $messages[$response->status()], 'level' => 'warning'], 'Errors/Show', [], $response->status()
                );
            case 500:
                if (!config('app.debug')) {
                    return Inertia::flash(
                        'banner', ['message' => $messages[$response->status()], 'level' => 'danger'], 'Errors/Show', [], $response->status()
                    );
                }
        }

        return $response;
    }
}
