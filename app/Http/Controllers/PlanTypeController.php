<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PlanTypeController extends Controller
{
    public function index(Request $request)
    {
        $planTypes = Cache::rememberForever('plan-types', fn() => PlanType::all());

        if ($request->wantsJson()) {
            return response()->json(['plan_types' => $planTypes]);
        }

        return Inertia::render('PlanTypes/Index', [
            'planTypes' => $planTypes
        ]);
    }

    public function store(Request $request)
    {
        PlanType::create($request->validate(['name' => 'required|string']));

        return back()->snackbar('Plan Type Created.');
    }

    public function show(PlanType $planType)
    {
        //
    }

    public function update(Request $request, PlanType $planType)
    {
        $planType->update($request->validate(['name' => 'required|string']));

        return back()->snackbar('Plan Type Updated.');
    }

    public function destroy(PlanType $planType)
    {
        // TODO Check if there are associated resources

        $planType->delete();

        return back()->snackbar('Plan Type Deleted.');
    }
}
