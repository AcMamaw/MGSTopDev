<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        // Fetch all reports with employee info
        $reports = Report::with('generatedBy')->get(); 
        return view('maincontent.reports', compact('reports'));
    }
}
