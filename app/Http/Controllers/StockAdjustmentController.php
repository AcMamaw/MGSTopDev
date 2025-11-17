<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        // Fetch all stock adjustments with stock and employees
        $adjustments = StockAdjustment::with([
            'stock.product', 
            'employee',
            'adjustedBy',
            'approvedBy'
        ])->get();

        return view('inventory.stockadjustment', compact('adjustments'));
    }
}
