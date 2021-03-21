<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function show(Member $member)
    {
//        $member->load(['membership' => fn($query) => $query->withTotalPaid()]);

        $member->loadMissing(
//            'dependents',
//            'membership.plan_type',
            'membership.subscriptions.payment_method',
//            'membership.loans.endorsements',
        );

//        $member->loadEndorsementsToMembers();

        $response = Inertia::render('Members/Show', [
           'member' => $member
        ]);

        return $response;
    }
}
