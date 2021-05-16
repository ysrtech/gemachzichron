<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDependentRequest;
use App\Models\Dependent;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DependentController extends Controller
{
    public function index(Member $member)
    {
        $member->load(['dependents' => fn($q) => $q->orderBy('dob')]);

        return Inertia::render('Members/Dependents/Index', [
            'member' => $member
                ->append('has_membership')
                ->only(['id', 'first_name', 'last_name', 'has_membership', 'deleted_at', 'dependents'])
        ]);
    }

    public function store(CreateDependentRequest $request, Member $member)
    {
        $member->dependents()->create($request->validated());

        return back()->snackbar('Child added.');
    }

    public function show(Dependent $dependent)
    {
        //
    }

    public function update(CreateDependentRequest $request, Dependent $dependent)
    {
        $dependent->update($request->validated());

        return back()->snackbar('Child updated.');
    }

    public function destroy(Request $request, Dependent $dependent)
    {
        if ($dependent->loans->isNotEmpty()) {
            if (!$request->boolean('confirm')) {
                return back()->withPartial('AlertModal', [
                    'icon'         => 'error',
                    'title'        => 'Delete Child Failed',
                    'message'      => "You cannot delete a child with associated loans.<br>
                              Do you want to remove <strong>$dependent->name</strong> from all associated loans?",
                    'actionButton' => [
                        'text'   => 'Continue',
                        'color'  => 'danger',
                        'route'  => route('dependents.destroy', ['dependent' => $dependent->id, 'confirm' => true]),
                        'method' => 'delete'
                    ],
                ]);
            }

            $dependent->loans->each(fn($loan) => $loan->update(['dependent_id' => null]));
        }

        if (!$request->boolean('confirm')) {
            return back()->withPartial('AlertModal', [
                'icon'         => 'error',
                'title'        => 'Are you sure?',
                'message'      => "Are you sure you want to delete <strong>$dependent->name</strong>?",
                'actionButton' => [
                    'text'   => 'Delete',
                    'color'  => 'danger',
                    'route'  => route('dependents.destroy', ['dependent' => $dependent->id, 'confirm' => true]),
                    'method' => 'delete'
                ],
            ]);
        }

        $dependent->delete();

        return back()->snackbar('Child deleted.');
    }
}
