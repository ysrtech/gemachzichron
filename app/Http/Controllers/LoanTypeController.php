<?php

namespace App\Http\Controllers;

use App\Models\LoanType;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{
    public function index()
    {
        return LoanType::all();
    }
}
