<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockInController extends Controller
{

    public function index()
    {
        // Get all stock-ins with product and employee
        $stockins = StockIn::with(['product', 'employee'])->get();

        // Get all products for the dropdown
        $products = Product::all();

        // Pass data to the view
        return view('inventory.instock', compact('stockins', 'products'));
    }
    
    public function store(Request $request)
    {
        // Validate input including product_type and size
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'product_type' => 'required|string|max:50', // Make sure product_type is required
            'size' => 'required|string|max:50',
            'quantity_product' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $employeeId = Auth::user()->employee_id;
        $total = $request->quantity_product * $request->unit_cost;

        // Create StockIn record
        $stockIn = StockIn::create([
            'employee_id' => $employeeId,
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
            'size' => $request->size,
            'quantity_product' => $request->quantity_product,
            'unit_cost' => $request->unit_cost,
            'total' => $total,
        ]);

        // Create corresponding Inventory record
        Inventory::create([
            'stockin_id' => $stockIn->stockin_id,
            'deliverydetails_id' => null,
            'product_id' => $request->product_id,
            'total_stock' => $request->quantity_product,
            'current_stock' => $request->quantity_product,
            'unit_cost' => $request->unit_cost,
            'size' => $request->size,
            'product_type' => $request->product_type, // <-- Add product_type here
            'date_received' => Carbon::now()->toDateString(),
            'received_by' => $employeeId,
            'last_updated' => now(),
        ]);

        // Return JSON if AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock-in added successfully!',
                'product_id' => $stockIn->product_id,
                'product_name' => $stockIn->product->product_name,
            ]);
        }

        return redirect()->back()->with('success', 'Stock-in added successfully and inventory updated!');
    }

    public function storeProduct(Request $request)
    {
        // Validate input
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
        ]);

        // Create product without supplier (for stock-in only)
        $product = Product::create([
            'supplier_id' => null, // No supplier for quick stock-in products
            'product_name' => $request->product_name,
            'description' => $request->description,
            'unit' => $request->unit,
        ]);

        // Return JSON for AJAX
        return response()->json([
            'success' => true,
            'product_id' => $product->product_id,
            'product_name' => $product->product_name,
            'description' => $product->description,
            'unit' => $product->unit
        ]);
    }
}