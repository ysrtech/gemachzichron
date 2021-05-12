<?php

use App\Http\Controllers\Auth\Account\AccountController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

//Route::get('/register', [RegisterController::class, 'create'])
//    ->middleware('guest')
//    ->name('register');
//
//Route::post('/register', [RegisterController::class, 'store'])
//    ->middleware('guest');

Route::get('/login', [AuthenticateController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticateController::class, 'store'])
    ->middleware('guest');

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->middleware(['guest'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.update');

Route::get('/verify-email', EmailVerificationPromptController::class)
    ->middleware(['auth'])
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/confirm-password', [ConfirmPasswordController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmPasswordController::class, 'store'])
    ->middleware(['auth']);

Route::post('/logout', [AuthenticateController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account', [AccountController::class, 'show'])
        ->name('account.show');

    Route::put('/account', [AccountController::class, 'update'])
        ->name('account.update');

    Route::delete('/account', [AccountController::class, 'destroy'])
        ->middleware('password.confirm')
        ->name('account.destroy');

    Route::put('/account/password', [AccountController::class, 'updatePassword'])
        ->name('account.password.update');

});
