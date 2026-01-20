<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class RolesController extends Controller
{
    public function index()
    {
        return Inertia::render('Roles/Index');
    }
}
