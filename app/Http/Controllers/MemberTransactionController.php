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
            'member' => $member->load(['transactions' => fn($q) => $q->orderByDesc('process_date')])
        ]);
    }
}
