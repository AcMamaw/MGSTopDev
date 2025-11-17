<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockIn;

class StockInController extends Controller
{
    public function index()
    {
        // Get all stock-ins with product and employee
        $stockins = StockIn::with(['product', 'employee'])->get();

        // Pass data to the view
        return view('inventory.instock', compact('stockins'));
    }
}
