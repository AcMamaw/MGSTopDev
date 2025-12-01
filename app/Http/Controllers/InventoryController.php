<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
  public function index()
{
    // Load all stock entries with relations
    $stocks = Inventory::with(['product', 'employee', 'stockin'])->get();

    // Group by product_id and sum totals
    $groupedStocks = $stocks->groupBy('product_id')->map(function ($group) {
        return (object) [
            'product'       => $group->first()->product,
            'total_stock'   => $group->sum('total_stock'),
            'current_stock' => $group->sum('current_stock'),
            'sizes'         => $group->pluck('size')->unique()->implode(', '), // <-- collect all sizes
        ];
    });

    return view('inventory.stock', [
        'groupedStocks' => $groupedStocks
    ]);
}

    
}
