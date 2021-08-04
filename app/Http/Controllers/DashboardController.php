<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'recent_transactions'  => Transaction::with(
                [
                    'member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed()
                ])
                ->orderByDesc('process_date')
                ->orderByDesc('id')
                ->take(20)
                ->get(),
            'pending_manual_count' => Transaction::where('gateway', \App\Gateways\Factory::MANUAL)
                ->where('status', Transaction::STATUS_PENDING)
                ->count(),
            'failed_count' => Transaction::where('status', Transaction::STATUS_FAIL)->count(),
            'month_success_total' => Transaction::where('status', Transaction::STATUS_SUCCESS)
                ->where('process_date', '>=', now()->startOfMonth()->toDateString())
            ->sum('amount')
        ]);
    }
}
