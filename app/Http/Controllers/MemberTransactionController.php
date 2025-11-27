<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberTransactionController extends Controller
{
    public function index(Member $member)
    {
        return Inertia::render('Members/Transactions/Index', [
            'member' => $member,
            'transactions' => $member->transactions()
                ->with('subscription:id,type', 'resolvingSubscriptions:id,resolves_transaction')
                ->orderByDesc('process_date')
                ->orderByDesc('id')
                ->paginate(20)
        ]);
    }
}
