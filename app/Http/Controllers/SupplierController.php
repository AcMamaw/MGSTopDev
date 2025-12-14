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


       public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'supplier_name'  => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_no'     => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:255',
        ]);

        $supplier->update($data);

        return response()->json($supplier);
    }

        public function archive($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->archive = 'Archived';
        $supplier->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->archive = null;
        $supplier->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        // if using soft deletes: $supplier->delete();
        $supplier->delete();

        return response()->json(['status' => 'ok']);
    }
}
