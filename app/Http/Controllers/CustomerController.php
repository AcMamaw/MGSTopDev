<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('management.customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($data);

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }
}
