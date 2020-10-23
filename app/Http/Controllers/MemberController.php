<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all('search', 'archived');

        return Inertia::render('Members/Index', [
            'filters' => $filters,
            'members' => Member::filter($filters)
                ->select(
                    'id',
                    'first_name',
                    'last_name',
                    'hebrew_name',
                    'email',
                    'home_phone',
                    'mobile_phone',
                    'deleted_at'
                )
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate()
                ->withQueryString()
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Member $member)
    {
        //
    }

    public function edit(Member $member)
    {
        //
    }

    public function update(Request $request, Member $member)
    {
        //
    }

    public function destroy(Member $member)
    {
        //
    }
}
