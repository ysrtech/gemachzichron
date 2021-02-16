<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Inertia\Inertia;
use Throwable;

trait InertiaHandler
{
    protected function prepareInertiaResponse($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            // First we will set the intended url in the session by using the
            // redirect guest method, but we won't actually return a redirect.
            redirect()->guest($e->redirectTo() ?? route('login'));

            // Now we will send an invalid inertia response which will be
            // handled in the frontend by opening the login modal.
            return response()->json(['message' => $e->getMessage()], 401);
        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        $status = parent::render($request, $e)->status();

        $message = [
                403 => "Forbidden",
                404 => "The page you are trying to access cannot be found.",
                419 => "The page expired, please try again.",
                500 => "Whoops, something went wrong on our servers.",
                503 => "We are doing some maintenance. Please check back soon.",
            ][$status] ?? false;

        if (!$message) {
            return parent::render($request, $e);
        }

        if ($status >= 400 && $status < 500) {
            return back()->snackbar($message);
        }

        return Inertia::render('Errors/Index', ['message' => $message])
            ->toResponse($request)
            ->setStatusCode($status);
    }

}
