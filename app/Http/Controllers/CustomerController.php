<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Show customers page.
     */
    public function index()
    {
        // You can change the view path if needed
        $customers = Customer::orderBy('customer_id')->get();

        return view('management.customer', compact('customers'));
    }

    /**
     * Store new customer (AJAX).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fname'      => 'required|string|max:255',
            'mname'      => 'nullable|string|max:255',
            'lname'      => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($data);

        // front‑end expects plain customer object
        return response()->json($customer);
    }

    /**
     * Update existing customer (AJAX).
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'fname'      => 'required|string|max:255',
            'mname'      => 'nullable|string|max:255',
            'lname'      => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        $customer->update($data);

        return response()->json($customer);
    }

    public function archive($id)
{
    $customer = Customer::findOrFail($id);
    $customer->archive = 'Archived';
    $customer->save();

    return response()->json(['status' => 'ok']);
}

    public function unarchive($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->archive = null;
        $customer->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete(); // or forceDelete() if soft deletes

        return response()->json(['status' => 'ok']);
    }
}
