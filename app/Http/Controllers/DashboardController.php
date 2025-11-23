<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Show main dashboard
    public function index()
    {
        return view('maincontent.dashboard');
    }
}
