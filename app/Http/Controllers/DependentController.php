<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDependentRequest;
use App\Models\Dependent;
use Illuminate\Http\Request;

class DependentController extends Controller
{
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
