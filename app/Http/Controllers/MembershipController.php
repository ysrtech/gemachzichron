<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Memberships/Index', [
            'filters' => $request->all('search', 'archived', 'membership_type', 'plan_type_id', 'active_membership', 'sort'),
            'members' =>  Member::search($request->search)
                ->filterWithTrashed($request->archived)
                ->filter($request->only('membership_type', 'plan_type_id'))
                ->filterBoolean($request->only('active_membership'))
                ->select([
                    'id', 'first_name', 'last_name', 'membership_since', 'active_membership', 'membership_type', 'plan_type_id', 'deleted_at'
                ])
                ->sort($request->sort)
                ->whereNotNull('membership_since')
                ->withMembershipPaymentsTotal()
                ->with('planType')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate()
        ]);
    }
}
