<?php

namespace App\Http\Controllers;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberDashboardController extends Controller
{
    
    public function index(Member $member)
    {


        $member->load([
            'loans' => fn($q) => $q->with('guarantors:id,first_name,last_name')
                ->withSum('transactions', 'amount')
                ->orderBy('loan_date', 'desc'),
            'dependents:member_id,id,name',
            'guarantees:id,loan_date,amount,member_id',
            'guarantees.member' => fn($q) => $q->select('id', 'first_name', 'last_name')->withTrashed(),
            'planType'
        ])->append('membership_payments_total');

        $member->loans->each->append('remaining_balance');
        $member->guarantees->each->append('remaining_balance');

        return Inertia::render('Members/Dashboard', [
            'member' => $member
        ]);
    }

  

   
   
}
