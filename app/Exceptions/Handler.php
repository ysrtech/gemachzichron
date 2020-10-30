<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, \Throwable $exception)
    {
        $response = parent::render($request, $exception);

        if ($request->header('X-Inertia')
            && !config('app.debug')
            && $message = $this->messages[$response->status()] ?? false
        ) {
            return Inertia::render('Error', ['message' => $message])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return $response;
    }

    protected array $messages = [
        403 => "Forbidden",
        404 => "Page Not Found",
        419 => "The page expired, please try again.",
        500 => "Whoops, something went wrong on our servers.",
        503 => "We are doing some maintenance. Please check back soon.",
    ];


}
