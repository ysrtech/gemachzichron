<?php

use App\Http\Controllers\DependentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanTypeController;
use App\Http\Controllers\SubscriptionController;
//use App\Http\Controllers\UserController;
//use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {

    Route::apiresource('memberships.subscriptions', SubscriptionController::class)->shallow();
    Route::apiresource('invoices', InvoiceController::class);
});


