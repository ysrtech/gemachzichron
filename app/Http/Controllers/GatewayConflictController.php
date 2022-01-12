<?php

namespace App\Http\Controllers;

use App\Models\GatewayConflict;
use Inertia\Inertia;

class GatewayConflictController extends Controller
{
    public function index()
    {
        return Inertia::render('GatewayConflicts/Index', [
           'conflicts' => GatewayConflict::with(['member:id,first_name,last_name', 'member.paymentMethods:id,member_id,gateway'])->get()
        ]);
    }
}
