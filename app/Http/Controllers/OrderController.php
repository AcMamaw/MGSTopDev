<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Stockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'customer',
            'items.stock.product',
            'payment'
        ])
        ->orderBy('order_date', 'desc')
        ->get();

        $customers = Customer::all();
        $inventories = Inventory::with('product')->get();
        $categories = Category::all();

        return view('maincontent.purchaseorder', compact(
            'orders',
            'customers',
            'inventories',
            'categories',
        ));
    }

    public function create()
    {
        $customers = Customer::all();
        $inventories = Inventory::with('product')->get();
        $categories = Category::all();

        return view('managestore.order-create', compact(
            'customers',
            'inventories',
            'categories',
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'category_id' => 'required|exists:categories,category_id',
                'order_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => 'required|exists:inventory,stock_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.color' => 'nullable|string',
                'items.*.size' => 'nullable|string',
                'total_amount' => 'required|numeric|min:0',
                'cash' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'reference_number' => 'nullable|string',
                'product_type' => 'nullable|string|in:stockin_id,deliverydetails_id',
            ]);

            DB::transaction(function() use ($request, &$order, &$payment) {

                // PAYMENT COMPUTATION
                $amount = $request->total_amount;
                $cash = $request->cash;
                $change_amount = max($cash - $amount, 0);
                $balance = max($amount - $cash, 0);
                $paymentStatus = ($balance > 0) ? 'Partial' : 'Fully Paid';

                // ORDER STATUS
                $orderStatus = 'Pending';
                if ($request->product_type === 'stockin_id' && $paymentStatus === 'Fully Paid') {
                    $orderStatus = 'Completed';
                }

                // CREATE ORDER
                $order = Order::create([
                    'customer_id' => $request->customer_id,
                    'category_id' => $request->category_id,
                    'order_date' => $request->order_date,
                    'ordered_by' => auth()->user()->employee->employee_id,
                    'total_amount' => $request->total_amount,
                    'status' => $orderStatus,
                ]);

                // LOOP THROUGH ORDER ITEMS
                foreach ($request->items as $item) {
                    $stockId = $item['stock_id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $color = $item['color'] ?? null;
                    $size = $item['size'] ?? null;

                    // Insert into orderdetails for ALL items
                    \DB::table('orderdetails')->insert([
                        'order_id' => $order->order_id,
                        'stock_id' => $stockId,
                        'color' => $color,
                        'size' => $size,
                        'quantity' => $quantity,
                        'price' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Only for Ready Made items: reduce inventory and create Stockout
                    if ($request->product_type === 'stockin_id') {
                        $inventory = Inventory::find($stockId);
                        if (!$inventory) continue;

                        if ($inventory->current_stock < $quantity) {
                            throw new \Exception("Insufficient stock for: {$inventory->product->product_name}. Available: {$inventory->current_stock}, Requested: {$quantity}");
                        }

                        $inventory->current_stock -= $quantity;
                        $inventory->last_updated = now();
                        $inventory->save();

                        Stockout::create([
                            'stock_id' => $stockId,
                            'employee_id' => auth()->user()->employee->employee_id,
                            'quantity_out' => $quantity,
                            'date_out' => now(),
                            'reason' => 'Order #' . $order->order_id . ' - Customer: ' . $order->customer->fname . ' ' . $order->customer->lname,
                            'status' => 'Completed',
                            'approved_by' => null,
                        ]);
                    }
                }

                // CREATE PAYMENT RECORD (both Ready Made and Customize items)
                $payment = Payment::create([
                    'order_id' => $order->order_id,
                    'employee_id' => auth()->user()->employee->employee_id,
                    'payment_date' => now(),
                    'amount' => $amount,
                    'cash' => $cash,
                    'change_amount' => $change_amount,
                    'balance' => $balance,
                    'status' => $paymentStatus,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                ]);
            });

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

            return response()->json([
                'success' => true,
                'message' => 'Order marked as Completed.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Order not found.'
        ], 404);
    }

   
    public function updatePayment(Request $request)
    {
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

            $payment = Payment::where('order_id', $validated['order_id'])->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found.'
                ], 404);
            }

            $payment->update([
                'cash' => $validated['cash'],
                'balance' => $validated['balance'],
                'status' => $validated['status'],
                'change_amount' => $validated['change_amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
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

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }

}