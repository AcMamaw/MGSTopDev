<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;  
use App\Models\Order;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        // Load payments with related orders and employees
        $payments = Payment::with(['order', 'employee'])->get();

        return view('maincontent.payment', compact('payments'));
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'cash' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:Fully Paid,Partial',
            'change_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,GCash',
            'reference_number' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Get the order
            $order = Order::findOrFail($validated['order_id']);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $validated['order_id'],
                'employee_id' => Auth::id(), // Current logged-in employee
                'payment_date' => now(),
                'amount' => $validated['amount'], // Amount applied to balance
                'cash' => $validated['cash'], // Total cash received
                'balance' => $validated['balance'], // Remaining balance
                'status' => $validated['status'], // 'Fully Paid' or 'Partial'
                'change_amount' => $validated['change_amount'], // Change to return
                'payment_method' => $validated['payment_method'], // 'Cash' or 'GCash'
                'reference_number' => $validated['reference_number'] ?? null, // GCash reference
            ]);

            // Update the order's balance and payment status
            $order->update([
                'balance' => $validated['balance'],
                'payment_status' => $validated['status']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'Fully Paid' 
                    ? 'Payment completed successfully! Order is fully paid.' 
                    : 'Partial payment recorded. Remaining balance: ₱' . number_format($validated['balance'], 2),
                'payment' => $payment,
                'change' => $validated['change_amount']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
