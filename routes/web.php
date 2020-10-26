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

Route::view('/', 'welcome')->name('welcome');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', Controllers\DashboardController::class)->name('dashboard');

    Route::apiResource('members', Controllers\MemberController::class);
    Route::put('members/{member}/restore', [Controllers\MemberController::class, 'restore'])->name('members.restore');
});
