<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;

class ProductController extends Controller
{
    // List products + suppliers for dropdown
    public function index()
    {
        $products  = Product::with('supplier')->get();
        $suppliers = Supplier::all();

        return view('managestore.product', compact('products', 'suppliers'));
    }

    // Store new product (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'  => 'required|exists:suppliers,supplier_id',
            'product_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'unit'         => 'required|string|max:50',
        ]);

        $product = Product::create($data);

        $product->load('supplier');

        return response()->json([
            'product_id'    => $product->product_id,
            'supplier_id'   => $product->supplier_id,
            'supplier_name' => $product->supplier->supplier_name ?? '',
            'product_name'  => $product->product_name,
            'description'   => $product->description,
            'unit'          => $product->unit,
        ]);
    }

   // Update existing product (AJAX)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'supplier_id'  => 'sometimes|nullable|exists:suppliers,supplier_id',
            'product_name' => 'sometimes|nullable|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'unit'         => 'sometimes|nullable|string|max:50',
        ]);

        // Update only provided fields
        $product->fill($data)->save();

        $product->load('supplier');

        return response()->json([
            'product_id'    => $product->product_id,
            'supplier_id'   => $product->supplier_id,
            'supplier_name' => $product->supplier->supplier_name ?? '',
            'product_name'  => $product->product_name,
            'description'   => $product->description,
            'unit'          => $product->unit,
        ]);
    }

    // Optional: return all as JSON (if ever needed)
    public function fetch()
    {
        $products = Product::with('supplier')->get();

        return response()->json($products);
    }

}
