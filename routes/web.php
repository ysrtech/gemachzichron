<?php

use App\Http\Controllers\DependentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberGuaranteesController;
use App\Http\Controllers\MembersExportController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanTypeController;
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
    Route::apiResource('users', UserController::class);
    Route::get('members/export', MembersExportController::class)->name('members.export');
    Route::resource('members', MemberController::class);
    Route::put('members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');
    Route::apiresource('members.dependents', DependentController::class)->shallow();
    Route::get('members/{member}/guarantees', [MemberGuaranteesController::class, 'index'])->name('members.guarantees.index');
    Route::post('members/{member}/membership', [MembershipController::class, 'store'])->name('members.membership.store');
    Route::apiResource('memberships', MembershipController::class)->only('index', 'update', 'destroy');
    Route::get('members/{member}/loans', [LoanController::class, 'indexForMember'])->name('members.loans.index');
    Route::apiResource('loans', LoanController::class)->except('store');
    Route::post('memberships/{membership}/loans', [LoanController::class, 'store'])->name('memberships.loans.store');
    Route::apiResource('plan-types', PlanTypeController::class);
});
