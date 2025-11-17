<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
   

    // Main content dashboard
    public function mainDashboard()
    {
        return view('maincontent.dashboard');
    }
}
