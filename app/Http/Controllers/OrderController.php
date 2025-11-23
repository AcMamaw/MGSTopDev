<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with customers and inventories.
        */public function index()
    {
        // Load all orders with customer, items, and payment
        $orders = Order::with([
            'customer',
            'items.stock.product',
            'payment'  // ← ADD THIS to load payment data
        ])
        ->orderBy('order_date', 'desc')
        ->get();

        // Load all customers
        $customers = Customer::all();

        // Load inventory with product relationship
        $inventories = Inventory::with('product')->get();

        // Return the view
        return view('maincontent.purchaseorder', compact('orders', 'customers', 'inventories'));
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'order_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => 'required|exists:inventory,stock_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'cash' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'reference_number' => 'nullable|string',
            ]);

            // Initialize variables
            $order = null;
            $payment = null;

            // Use transaction
            DB::transaction(function() use ($request, &$order, &$payment) {

                // 1. Create the order
                $order = Order::create([
                    'customer_id' => $request->customer_id,
                    'order_date' => $request->order_date,
                    'ordered_by' => auth()->user()->employee->employee_id,
                    'total_amount' => $request->total_amount,
                    'status' => 'In Progress',
                ]);

                // 2. Create order items (WITHOUT total column)
                foreach ($request->items as $item) {
                    $order->items()->create([
                        'stock_id' => $item['stock_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        // Removed 'total' - it will be calculated dynamically
                    ]);
                }

                // 3. Calculate payment details
                $amount = $request->total_amount;
                $cash = $request->cash;
                $change_amount = max($cash - $amount, 0);
                $balance = max($amount - $cash, 0);
                $status = ($balance > 0) ? 'Partial' : 'Fully Paid';

                // 4. Create payment record
                $payment = Payment::create([
                    'order_id' => $order->order_id,
                    'employee_id' => auth()->user()->employee->employee_id,
                    'payment_date' => now(),
                    'amount' => $amount,
                    'cash' => $cash,
                    'change_amount' => $change_amount,
                    'balance' => $balance,
                    'status' => $status,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                ]);
            });

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Order and payment created successfully',
                'order' => $order->load('items'),
                'payment' => $payment,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Order Store Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
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


    public function markAsCompleted($orderId)
{
    $order = Order::find($orderId);

    if ($order) {
        $order->status = 'Completed';
        $order->save();
        return response()->json(['success' => true, 'message' => 'Order marked as Completed.']);
    }

    return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
}

    public function updatePayment(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'cash' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:Fully Paid,Partial',
            'change_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,GCash',
            'reference_number' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Find the existing payment record
            $payment = Payment::where('order_id', $validated['order_id'])->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found for this order.'
                ], 404);
            }

            // UPDATE only the specified fields
            $payment->update([
                'cash' => $validated['cash'], // Update cash
                'balance' => $validated['balance'], // Update balance
                'status' => $validated['status'], // Update status
                'change_amount' => $validated['change_amount'], // Update change
                'payment_method' => $validated['payment_method'], // Update method
                'reference_number' => $validated['reference_number'], // Update reference
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'Fully Paid' 
                    ? 'Payment completed successfully!' 
                    : 'Payment updated. Remaining balance: ₱' . number_format($validated['balance'], 2),
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Payment Update Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }


}
