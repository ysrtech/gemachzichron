<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Inertia\Inertia;

class MemberGuaranteesController extends Controller
{
    public function index(Member $member)
    {
        $member->load([
            'guarantees:id,loan_date,amount,membership_id',
            'guarantees.membership:id,member_id',
            'guarantees.membership.member' => fn($q) => $q->select('id', 'first_name', 'last_name')->withTrashed()
        ]);

        return Inertia::render('Members/Guarantees/Index', [
            'member' => $member->only(['id', 'first_name', 'last_name', 'deleted_at', 'guarantees'])
        ]);
    }
}
