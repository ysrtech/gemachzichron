<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Subscription;
use App\Models\Member;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalLoansGiven = Loan::sum('amount');
        $loansCount = Loan::count();
        $membersCount = Member::where('active_membership',1)->count();
        $totalLoansPayments = Transaction::where('status', Transaction::STATUS_SUCCESS)
        ->whereHas('subscription', fn($q) => $q->where('type', Subscription::TYPE_LOAN_PAYMENT))->sum('amount');

        return Inertia::render('Dashboard', [
            'recent_transactions'  => Transaction::with(
                [
                    'member' => fn($q) => $q->select(['id', 'first_name', 'last_name', 'deleted_at'])->withTrashed(),
                    'subscription:id,type'
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
            ->sum('amount'),
            'total_capital' => Transaction::where('status', Transaction::STATUS_SUCCESS)
                ->where('type',Transaction::TYPE_MAIN_TRANSACTION)
                ->whereHas('subscription', fn($q) => $q->whereIn('type', [Subscription::TYPE_MEMBERSHIP,Subscription::TYPE_PIKUDON]))
                ->sum('amount'),
            'total_membership_fees' => Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->where('type',Transaction::TYPE_MEMBERSHIP_FEE)
            ->orWhere('type',Transaction::TYPE_DECLINE_FEE)
            ->sum('amount'),
            'total_loans' => $totalLoansGiven,
            'total_loans_outstanding' => ($totalLoansGiven - $totalLoansPayments),
            'loans_count' => $loansCount,
            'members_count' => $membersCount,
        ]);
    }
}
