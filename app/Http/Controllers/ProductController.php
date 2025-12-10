<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    // List products + suppliers + categories for dropdown
    public function index()
    {
        $products   = Product::with(['supplier', 'category'])->get();
        $suppliers  = Supplier::all();
        $categories = Category::all();

        return view('managestore.product', compact('products', 'suppliers', 'categories'));
    }

    // Store new product (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'  => 'required|exists:suppliers,supplier_id',
            'category_id'  => 'required|exists:categories,category_id',
            'product_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'unit'         => 'required|string|max:50',
            'markup_rule'  => 'nullable|numeric',
        ]);

        if (!isset($data['markup_rule'])) {
            $data['markup_rule'] = 0;
        }

        $product = Product::create($data);
        $product->load(['supplier', 'category']);

        return response()->json([
            'product_id'     => $product->product_id,
            'supplier_id'    => $product->supplier_id,
            'supplier_name'  => $product->supplier->supplier_name ?? '',
            'category_id'    => $product->category_id,
            'category_name'  => $product->category->category_name ?? '',
            'product_name'   => $product->product_name,
            'description'    => $product->description,
            'unit'           => $product->unit,
            'markup_rule'    => $product->markup_rule,
        ]);
    }

    // Update existing product (AJAX)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'supplier_id'  => 'sometimes|nullable|exists:suppliers,supplier_id',
            'category_id'  => 'sometimes|nullable|exists:categories,category_id',
            'product_name' => 'sometimes|nullable|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'unit'         => 'sometimes|nullable|string|max:50',
            'markup_rule'  => 'sometimes|nullable|numeric',
        ]);

        if (array_key_exists('markup_rule', $data) && $data['markup_rule'] === null) {
            $data['markup_rule'] = 0;
        }

        $product->fill($data)->save();
        $product->load(['supplier', 'category']);

        return response()->json([
            'product_id'     => $product->product_id,
            'supplier_id'    => $product->supplier_id,
            'supplier_name'  => $product->supplier->supplier_name ?? '',
            'category_id'    => $product->category_id,
            'category_name'  => $product->category->category_name ?? '',
            'product_name'   => $product->product_name,
            'description'    => $product->description,
            'unit'           => $product->unit,
            'markup_rule'    => $product->markup_rule,
        ]);
    }

    // Optional: return all as JSON
    public function fetch()
    {
        $products = Product::with(['supplier', 'category'])->get();
        return response()->json($products);
    }
  
    public function updateImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // 2MB
        ]);

        // delete old file if present
        if ($product->image_path && str_starts_with($product->image_path, 'storage/')) {
            $oldRelative = str_replace('storage/', '', $product->image_path);
            Storage::disk('public')->delete($oldRelative);
        }

        // store new file
        $path = $request->file('image')->store('products', 'public'); // storage/app/public/products

        $product->image_path = 'storage/' . $path; // for asset()
        $product->save();

        return response()->json([
            'product_id' => $product->product_id,
            'image_path' => asset($product->image_path),
        ]);
    }

      public function archive(Product $product)
    {
        $product->archive = 'Archived';
        $product->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive(Product $product)
    {
        $product->archive = null;
        $product->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['status' => 'ok']);
    }

}
