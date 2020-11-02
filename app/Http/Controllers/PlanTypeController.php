<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use Illuminate\Http\Request;

class PlanTypeController extends Controller
{
    public function index()
    {
        return response()->json(['plan_types' => PlanType::all()]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(PlanType $planType)
    {
        //
    }

    public function update(Request $request, PlanType $planType)
    {
        //
    }

    public function destroy(PlanType $planType)
    {
        //
    }
}
