<?php

use App\Http\Controllers\DependentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanTypeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Providers\RouteServiceProvider;
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

    Route::redirect('/', RouteServiceProvider::HOME);
    Route::apiResource('users', UserController::class);
    Route::get('members/export', [MemberController::class, 'export'])->name('members.export');
    Route::apiResource('members', MemberController::class);
    Route::put('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');
    Route::apiresource('members.dependents', DependentController::class)->shallow();
    Route::post('members/{member}/memberships', [MembershipController::class, 'store'])->name('members.memberships.store');
    Route::apiresource('memberships', MembershipController::class)->except('store');
    Route::apiresource('memberships.subscriptions', SubscriptionController::class)->shallow();
    Route::apiresource('memberships.loans', LoanController::class)->shallow();
    Route::apiresource('plan-types', PlanTypeController::class);
    Route::apiresource('invoices', InvoiceController::class);
});


