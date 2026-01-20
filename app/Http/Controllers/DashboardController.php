<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'message' => 'Welcome to Gemach Zichron AY - Fresh Start!'
        ]);
    }
}
