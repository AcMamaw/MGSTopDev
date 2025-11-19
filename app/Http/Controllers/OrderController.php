<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with customers and inventories.
     */
    public function index()
    {
        // Load all orders with customer and items
        $orders = Order::with(['customer', 'items.stock.product'])->get();

        // Load all customers
        $customers = Customer::all();

        // Load inventory with product relationship
        $inventories = Inventory::with('product')->get();

        // Return the view
        return view('maincontent.purchaseorder', compact('orders', 'customers', 'inventories'));
    }

        public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|exists:inventory,stock_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // Create the order with default status
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'ordered_by' => auth()->user()->employee->employee_id,
                'total_amount' => $request->total_amount,
                'status' => 'Pending',
            ]);

            // Loop through order items
            foreach ($request->items as $item) {

                // Create orderdetails record (without 'total')
                $order->items()->create([
                    'stock_id' => $item['stock_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Stock deduction removed
            }
        });

        return redirect()->route('purchaseorder')
                        ->with('success', 'Order created successfully!');
    }

        
        public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }


}
