<?php

use App\Http\Controllers\DataExportController;
use App\Http\Controllers\MemberDependentController;
use App\Http\Controllers\MemberLoanController;
use App\Http\Controllers\MemberPaymentMethodController;
use App\Http\Controllers\MemberSubscriptionController;
use App\Http\Controllers\MemberTransactionController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\DependentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberGuaranteesController;
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
    Route::resource('members', MemberController::class);
    Route::put('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');

    // Memberships
    Route::get('memberships', [MembershipController::class, 'index'])->name('memberships.index');

    // Loans
    Route::get('members/{member}/loans', [MemberLoanController::class, 'index'])->name('members.loans.index');
    Route::post('members/{member}/loans', [MemberLoanController::class, 'store'])->name('members.loans.store');
    Route::apiResource('loans', LoanController::class)->except('store');

    // Subscriptions
    Route::get('members/{member}/subscriptions', [MemberSubscriptionController::class, 'index'])->name('members.subscriptions.index');
    Route::post('members/{member}/subscriptions', [MemberSubscriptionController::class, 'store'])->name('members.subscriptions.store');
    Route::apiResource('subscriptions', SubscriptionController::class)->except('store');

    // Transactions
    Route::get('members/{member}/transactions', [MemberTransactionController::class, 'index'])->name('members.transactions.index');
    Route::post('members/{member}/transactions', [MemberTransactionController::class, 'store'])->name('members.transactions.store');
    Route::apiResource('transactions', TransactionController::class)->except('store');

    // Guarantees
    Route::get('members/{member}/guarantees', [MemberGuaranteesController::class, 'index'])->name('members.guarantees.index');

    // Payment Methods
    Route::get('members/{member}/payment-methods', [MemberPaymentMethodController::class, 'index'])->name('members.payment-methods.index');
    Route::post('members/{member}/payment-methods',[MemberPaymentMethodController::class, 'store'])->name('members.payment-methods.store');
    Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');

    // Dependents
    Route::get('members/{member}/dependents', [MemberDependentController::class, 'index'])->name('members.dependents.index');
    Route::post('members/{member}/dependents', [MemberDependentController::class, 'store'])->name('members.dependents.store');
    Route::put('dependents/{dependent}', [DependentController::class, 'update'])->name('dependents.update');
    Route::delete('dependents/{dependent}', [DependentController::class, 'destroy'])->name('dependents.destroy');

    // Notes
    Route::post('notes/{noteableType}/{noteableId}', [NoteController::class, 'store'])->name('notes.store');
    Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Plan Types
    Route::apiResource('plan-types', PlanTypeController::class);

    // Exports
    Route::get('export', [DataExportController::class, 'index'])->name('export.index');
    Route::get('export/{model}', [DataExportController::class, 'show'])->name('export.show');
});
