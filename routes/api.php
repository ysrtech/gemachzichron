<?php

use App\Models\Member;
use App\Models\PlanType;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {

    Route::get('members', function (Request $request) {
        return Member::search($request->search)
            ->filterWithTrashed($request->archived)
            ->filterNull($request->only('membership_since'))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->when($request->with, fn($query, $with) => $query->with($with))
            ->when($request->limit, fn($query, $limit) => $query->limit($limit))
            ->get()
            ->toArray();
    })->name('members.index');

    Route::get('members/{member}', function (Request $request, Member $member) {

    })->name('members.show');

    Route::get('subscriptions', function (Subscription $subscription) {
        return $subscription->toArray();
    })->name('subscriptions.show');

    Route::get('plan-types', function () {
        return response()->json([
            'plan_types' => Cache::rememberForever('plan-types', fn() => PlanType::all())
        ]);
    })->name('plan-types.index');
});
