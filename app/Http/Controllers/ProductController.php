<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier; // add this

class ProductController extends Controller
{
    public function index()
    {
        // Load products with supplier relationship
        $products = Product::with('supplier')->get();

        // Load all suppliers for the dropdown
        $suppliers = Supplier::all();

        return view('managestore.product', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        // Create product
        $product = Product::create([
            'supplier_id' => $request->supplier_id,
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit,
        ]);

        // Return supplier name for dynamic table row
        $product->load('supplier');

        return response()->json([
            'product_id' => $product->product_id,
            'supplier_id' => $product->supplier_id,
            'supplier_name' => $product->supplier->supplier_name, // added
            'product_name' => $product->product_name,
            'description' => $product->description,
            'price' => $product->price,
            'unit' => $product->unit
        ]);
    }
}
