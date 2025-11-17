<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOut;

class StockOutController extends Controller
{
    public function index()
    {
        // Eager load stock, employee, and approver info
        $outstock = StockOut::with(['stock', 'employee', 'approver'])->get();

        // Pass $outstock to the view
        return view('inventory.outstock', compact('outstock'));
    }
}
