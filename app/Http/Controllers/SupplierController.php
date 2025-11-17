<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('management.supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {
        // Check if JSON
        $data = $request->json()->all() ?: $request->all();

        $validated = validator($data, [
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ])->validate();

        $supplier = Supplier::create($validated);

        return response()->json($supplier);
    }

}
