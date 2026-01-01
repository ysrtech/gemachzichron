<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\GatewayConflictController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MailLogController;
use App\Http\Controllers\MemberActivityController;
use App\Http\Controllers\MemberDependentController;
use App\Http\Controllers\MemberLoanController;
use App\Http\Controllers\MemberWithdrawalController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberPaymentMethodController;
use App\Http\Controllers\MemberSubscriptionController;
use App\Http\Controllers\MemberTransactionController;
use App\Http\Controllers\DependentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberGuaranteesController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanTypeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionTransactionController;
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

// Mailgun webhook (public route)
Route::post('webhooks/mailgun', [MailLogController::class, 'webhook'])->name('webhooks.mailgun');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Users
    Route::apiResource('users', UserController::class);

    // Members
    Route::resource('members', MemberController::class);
    Route::put('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');
    Route::get('members/{member}/dashboard', [MemberDashboardController::class, 'index'])->name('members.dashboard');
    Route::get('members/{member}/transactionsreport', [MemberController::class, 'transactionsreport'])->name('members.transactionsreport');

    // Memberships
    Route::get('memberships', [MembershipController::class, 'index'])->name('memberships.index');

    // Loans
    Route::get('members/{member}/loans', [MemberLoanController::class, 'index'])->name('members.loans.index');
    Route::post('members/{member}/loans', [MemberLoanController::class, 'store'])->name('members.loans.store');
    Route::apiResource('loans', LoanController::class)->except('store');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loan.destroy');

    // Withdrawals
    Route::get('members/{member}/withdrawals', [MemberWithdrawalController::class, 'index'])->name('members.withdrawals.index');
    Route::post('members/{member}/withdrawals', [MemberWithdrawalController::class, 'store'])->name('members.withdrawals.store');
    Route::apiResource('withdrawals', WithdrawalController::class)->except('store');
    Route::delete('withdrawals/{withdrawal}', [WithdrawalController::class, 'destroy'])->name('withdrawal.destroy');

    // Subscriptions
    Route::get('members/{member}/subscriptions', [MemberSubscriptionController::class, 'index'])->name('members.subscriptions.index');
    Route::post('members/{member}/subscriptions', [MemberSubscriptionController::class, 'store'])->name('members.subscriptions.store');
    Route::apiResource('subscriptions', SubscriptionController::class)->except('store');
    Route::post('subscriptions/{subscription}/refresh', [SubscriptionController::class, 'refresh'])->name('subscription.refresh');
    Route::post('subscriptions/{subscription}/destroy', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');


    // Transactions
    Route::get('members/{member}/transactions', [MemberTransactionController::class, 'index'])->name('members.transactions.index');
    Route::patch('transactions/{transaction}/resolve', [TransactionController::class, 'resolve'])->name('transaction.resolve');
    Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    Route::post('subscriptions/{subscription}/transactions', [SubscriptionTransactionController::class, 'store'])->name('subscriptions.transactions.store');
    Route::apiResource('transactions', TransactionController::class)->except(['store', 'update', 'destroy']);

    // Activities
    Route::get('members/{member}/activities', [MemberActivityController::class, 'index'])->name('members.activities.index');
    Route::post('members/{member}/activities', [MemberActivityController::class, 'store'])->name('members.activities.store');
    Route::put('members/{member}/activities/{note}', [MemberActivityController::class, 'update'])->name('members.activities.update');
    Route::delete('members/{member}/activities/{note}', [MemberActivityController::class, 'destroy'])->name('members.activities.destroy');

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

    // Gateway Conflicts
    Route::get('conflicts', [GatewayConflictController::class, 'index'])->name('conflicts.index');

    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Mail Logs
    Route::get('mail-logs', [MailLogController::class, 'index'])->name('mail-logs.index');
    Route::get('mail-logs/{mailLog}', [MailLogController::class, 'show'])->name('mail-logs.show');
    
    // Test Email Route
    Route::get('test-email', function(\Illuminate\Http\Request $request) {
        $email = $request->input('email', auth()->user()->email);
        \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\TestEmail());
        return redirect()->route('mail-logs.index')->with('success', 'Test email sent to ' . $email);
    })->name('test-email');

    // Plan Types
    Route::apiResource('plan-types', PlanTypeController::class);

    // Exports
    Route::get('export', [DataExportController::class, 'index'])->name('export.index');
    Route::get('export/{model}', [DataExportController::class, 'show'])->name('export.show');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/plan-types', [SettingsController::class, 'storePlanType'])->name('settings.plan-types.store');
    Route::put('settings/plan-types/{planType}', [SettingsController::class, 'updatePlanType'])->name('settings.plan-types.update');
    Route::patch('settings/plan-types/{planType}/rate', [SettingsController::class, 'updatePlanTypeRate'])->name('settings.plan-types.rate.update');
    Route::delete('settings/plan-types/{planType}/rate', [SettingsController::class, 'deletePlanTypeRate'])->name('settings.plan-types.rate.delete');
    Route::delete('settings/plan-types/{planType}', [SettingsController::class, 'destroyPlanType'])->name('settings.plan-types.destroy');
    Route::post('settings/loan-types', [SettingsController::class, 'storeLoanType'])->name('settings.loan-types.store');
    Route::post('settings/loan-types/{loanType}', [SettingsController::class, 'updateLoanType'])->name('settings.loan-types.update');
    Route::delete('settings/loan-types/{loanType}', [SettingsController::class, 'destroyLoanType'])->name('settings.loan-types.destroy');
});
