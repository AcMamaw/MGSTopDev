<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Product;

class InventoryController extends Controller
{
    public function index()
    {
        $stocks = Inventory::with(['product.supplier', 'employee', 'stockin', 'deliveryDetail'])
            ->whereHas('product')   
            ->get();

        $groupedStocks = $stocks
            ->groupBy(function ($row) {
                return $row->product_id.'|'.($row->product_type ?? 'N/A');
            })
            ->map(function ($group) {
                $first = $group->first();

                return (object) [
                    'product'             => $first->product,
                    'product_type'        => $first->product_type ?? 'N/A',
                    'total_stock'         => $group->sum('total_stock'),
                    'current_stock'       => $group->sum('current_stock'),
                    'sizes'               => $group->pluck('size')->unique()->implode(', '),
                    // extra fields for the modal
                    'supplier_id'         => $first->product->supplier_id ?? null,
                    'supplier_name'       => $first->product->supplier->supplier_name ?? '',
                    'product_id'          => $first->product->product_id ?? null,
                    'product_name'        => $first->product->product_name ?? '',
                    'unit_cost'           => $first->unit_cost ?? 0, // ✅ Added unit cost
                ];
            });

        $suppliers = Supplier::all();
        $products  = Product::all();

        return view('inventory.stock', [
            'groupedStocks' => $groupedStocks,
            'suppliers'     => $suppliers,
            'products'      => $products,
        ]);
    }
}