<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Inertia\Inertia;
use Throwable;

trait InertiaHandler
{
    protected function prepareInertiaResponse($request, Throwable $e)
    {
        if ($request->inertia() && $e instanceof AuthenticationException) {
            // First we will put the intended url in the session by using the
            // redirect guest method, but we won't actually return a redirect.
            redirect()->guest($e->redirectTo() ?? route('login'));

            // Now we will send an invalid inertia response which will be
            // handled in the frontend by opening the login modal.
            return response()->json(['message' => $e->getMessage()], 401);
        }

        $response = parent::render($request, $e);

        if ($response->status() === 419) {
            return back()->banner('The page expired, please try again', 'warning');
        }

        if (config('app.debug')) {
            return $response;
        }

        $messages = [
            403 => $e->getMessage() ?: 'Forbidden',
            404 => 'Page Not Found',
            429 => 'Too Many Requests',
            500 => 'Whoops, something went wrong on our servers',
            503 => 'We are doing some maintenance, Please check back soon',
        ];

        if (!array_key_exists($response->status(), $messages)) {
            return $response;
        }

        return Inertia::render('Errors/Show', ['message' => $messages[$response->status()]])
            ->toResponse($request)
            ->setStatusCode($response->status());
    }
}
