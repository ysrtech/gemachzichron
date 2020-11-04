<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::redirect('/', 'dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', Controllers\DashboardController::class)->name('dashboard');
    Route::apiResource('users', Controllers\UserController::class);
    Route::get('members/export', [Controllers\MemberController::class, 'export'])->name('members.export');
    Route::apiResource('members', Controllers\MemberController::class);
    Route::put('members/{member}/restore', [Controllers\MemberController::class, 'restore'])->name('members.restore');
    Route::apiresource('members.dependents', Controllers\DependentController::class)->shallow();
    Route::post('members/{member}/memberships', [Controllers\MembershipController::class, 'store'])->name('members.memberships.store');
    Route::apiresource('memberships', Controllers\MembershipController::class)->except('store');
    Route::apiresource('memberships.subscriptions', Controllers\SubscriptionController::class)->shallow();
    Route::apiresource('memberships.loans', Controllers\LoanController::class)->shallow();
    Route::apiresource('plan-types', Controllers\PlanTypeController::class);
});
