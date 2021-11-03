<?php

namespace App\Http\Controllers;

use App\Models\GatewayConflict;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GatewayConflictController extends Controller
{
    public function index()
    {
        return Inertia::render('GatewayConflicts/Index', [
           'conflicts' => GatewayConflict::with(['member:id,first_name,last_name', 'member.paymentMethods:id,member_id,gateway'])->get()
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

    public function show(GatewayConflict $gatewayConflict)
    {
        //
    }

    public function edit(GatewayConflict $gatewayConflict)
    {
        //
    }

    public function update(Request $request, GatewayConflict $gatewayConflict)
    {
        //
    }

    public function destroy(GatewayConflict $gatewayConflict)
    {
        //
    }
}
