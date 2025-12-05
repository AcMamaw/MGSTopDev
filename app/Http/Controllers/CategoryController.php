<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
        public function index()
    {
        $categories = Category::orderBy('category_id', 'desc')->get();
        return view('managestore.category', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);

        return response()->json($category);
    }

      public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'category_name' => 'sometimes|nullable|string|max:255',
            'description'   => 'sometimes|nullable|string',
        ]);

        $category->fill($data)->save();

        return response()->json([
            'category_id'   => $category->category_id,
            'category_name' => $category->category_name,
            'description'   => $category->description,
        ]);
    }

}
