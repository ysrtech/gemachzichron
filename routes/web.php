<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\DependentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberGuaranteesController;
use App\Http\Controllers\MembersExportController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanTypeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
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
Route::redirect('/', RouteServiceProvider::HOME);

Route::middleware(['auth'])->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    // Users
    Route::apiResource('users', UserController::class);

    // Members
    Route::get('members/export', MembersExportController::class)->name('members.export');
    Route::resource('members', MemberController::class);
    Route::put('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');

    // Memberships
    Route::post('members/{member}/membership', [MembershipController::class, 'store'])->name('members.membership.store');
    Route::apiResource('memberships', MembershipController::class)->only('index', 'update', 'destroy');

    // Loans
    Route::get('members/{member}/loans', [LoanController::class, 'indexForMember'])->name('members.loans.index');
    Route::apiResource('loans', LoanController::class)->except('store');
    Route::post('memberships/{membership}/loans', [LoanController::class, 'store'])->name('memberships.loans.store');

    // Subscriptions
    Route::get('members/{member}/subscriptions', [SubscriptionController::class, 'indexForMember'])->name('members.subscriptions.index');
    Route::apiResource('subscriptions', SubscriptionController::class)->except('store');
    Route::post('memberships/{membership}/subscriptions', [SubscriptionController::class, 'store'])->name('memberships.subscriptions.store');

    // Transactions
    Route::get('members/{member}/transactions', [TransactionController::class, 'indexForMember'])->name('members.transactions.index');
    Route::apiResource('transactions', TransactionController::class)->except('store');
    Route::post('memberships/{membership}/transactions', [TransactionController::class, 'store'])->name('memberships.transactions.store');

    // Guarantees
    Route::get('members/{member}/guarantees', [MemberGuaranteesController::class, 'index'])->name('members.guarantees.index');

    // PaymentMethods
    Route::get('members/{member}/payment-methods', [PaymentMethodController::class, 'index'])->name('members.payment-methods.index');
    Route::post('memberships/{membership}/payment-methods',[PaymentMethodController::class, 'store'])->name('memberships.payment-methods.store');
    Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');

    // Children
    Route::apiresource('members.dependents', DependentController::class)->shallow();

    // Comments
    Route::post('notes/{noteableType}/{noteableId}', [NoteController::class, 'store'])->name('notes.store');
    Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Plan Types
    Route::apiResource('plan-types', PlanTypeController::class);
});
