<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'planTypes' => PlanType::withCount('members')->get(),
            'loanTypes' => LoanType::all(),
        ]);
    }

    public function storePlanType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:plan_types,name',
        ]);

        PlanType::create($validated);

        return back()->snackbar('Plan type created.');
    }

    public function updatePlanType(Request $request, PlanType $planType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:plan_types,name,' . $planType->id,
        ]);

        $planType->update($validated);

        return back()->snackbar('Plan type updated.');
    }

    public function destroyPlanType(PlanType $planType)
    {
        // Check if plan type is being used
        if ($planType->members()->exists()) {
            return back()->snackbar('Cannot delete plan type that is in use.', 'error');
        }

        $planType->delete();

        return back()->snackbar('Plan type deleted.');
    }

    public function updatePlanTypeRate(Request $request, PlanType $planType)
    {
        $validated = $request->validate([
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date_format:Y-m-d',
        ]);

        $rates = $planType->rates ?? [];
        
        // If there's an old date being edited, remove it
        if ($request->has('old_effective_date') && $request->old_effective_date) {
            unset($rates[$request->old_effective_date]);
        }
        
        $rates[$validated['effective_date']] = (float) $validated['rate'];
        
        // Sort by date in descending order (newest first)
        krsort($rates);

        $planType->update(['rates' => $rates]);

        return back()->snackbar('Rate updated.');
    }

    public function deletePlanTypeRate(Request $request, PlanType $planType)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $rates = $planType->rates ?? [];
        unset($rates[$validated['date']]);

        $planType->update(['rates' => $rates]);

        return back()->snackbar('Rate deleted.');
    }

    public function storeLoanType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:loan_types,name',
        ]);

        LoanType::create($validated);

        return back()->snackbar('Loan type created.');
    }

    public function updateLoanType(Request $request, LoanType $loanType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:loan_types,name,' . $loanType->id,
        ]);

        $loanType->update($validated);

        return back()->snackbar('Loan type updated.');
    }

    public function destroyLoanType(LoanType $loanType)
    {
        $loanType->delete();

        return back()->snackbar('Loan type deleted.');
    }
}
