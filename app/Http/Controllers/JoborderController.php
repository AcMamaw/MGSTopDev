<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Joborder;
use App\Models\OrderDetail;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Stockout;
use Illuminate\Support\Facades\DB;

class JoborderController extends Controller
{
    public function index()
{
    $employeeId = auth()->user()->employee->employee_id;

    $orders = Order::with([
        'customer',
        'items.stock.product',
        'category',
        'items.jobOrders' => function($query) use ($employeeId) {
            $query->where('made_by', $employeeId);
        }
    ])
    ->where('assigned_to', $employeeId) 
    ->whereIn('status', ['In Progress', 'Released', 'Pending'])
    ->orderBy('order_date', 'desc')
    ->get()
    ->map(function($order) {
        // Check if order has been picked by checking if job orders exist
        $order->is_picked = $order->items->some(function($item) {
            return $item->jobOrders->isNotEmpty();
        });
        return $order;
    });

    // ✅ ADD THIS - Get completed job orders
    $completedJobOrders = Joborder::where('made_by', $employeeId)
        ->where('status', 'Completed')
        ->orderBy('joborder_end', 'desc')
        ->get();

    return view('maincontent.joborder', compact('orders', 'completedJobOrders'));
}

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'orderdetails_id' => 'required|exists:orderdetails,orderdetails_id',
            'joborder_created' => 'required|date',
            'joborder_end' => 'nullable|date',
            'estimated_time' => 'required|integer',
            'status' => 'required|string|max:50',
            'made_by' => 'required|exists:employees,employee_id',
        ]);

        Joborder::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job order created successfully!'
        ]);
    }

    
    public function pickJobOrder(Request $request, $orderId)
    {
        try {
            DB::beginTransaction();

            $employeeId = auth()->user()->employee->employee_id;

            // Find order and verify it's assigned to this employee
            $order = Order::with('items.stock.product', 'customer')
                ->where('order_id', $orderId)
                ->where('assigned_to', $employeeId)
                ->firstOrFail();

            // Check if already picked by checking if job orders exist for this order
            $hasJobOrders = Joborder::whereIn('orderdetails_id', $order->items->pluck('orderdetails_id'))
                ->where('made_by', $employeeId)
                ->exists();

            if ($hasJobOrders) {
                throw new \Exception('This order has already been picked.');
            }

            // Check if order is already released
            if ($order->status === 'Released') {
                throw new \Exception('This order is already released.');
            }

            // Process each order item
            foreach ($order->items as $item) {
                $inventory = Inventory::find($item->stock_id);
                
                if (!$inventory) {
                    throw new \Exception("Stock item not found: {$item->stock_id}");
                }

                // Check if enough stock available
                if ($inventory->current_stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for: {$inventory->product->product_name}. Available: {$inventory->current_stock}, Required: {$item->quantity}");
                }

                // Reduce inventory
                $inventory->current_stock -= $item->quantity;
                $inventory->last_updated = now();
                $inventory->save();

                // Create stockout entry
                Stockout::create([
                    'stock_id' => $item->stock_id,
                    'employee_id' => $employeeId,
                    'quantity_out' => $item->quantity,
                    'date_out' => now(),
                    'reason' => 'Job Order Picked - Order #' . $order->order_id . ' - Customer: ' . ($order->customer->fname ?? '') . ' ' . ($order->customer->lname ?? ''),
                    'size' => $item->size ?? null,
                    'status' => 'Picked',
                    'approved_by' => null,
                ]);

                // Create job order entry
                Joborder::create([
                    'orderdetails_id' => $item->orderdetails_id,
                    'joborder_created' => now(),
                    'joborder_end' => null,
                    'estimated_time' => 24, // Default 24 hours
                    'status' => 'In Progress',
                    'made_by' => $employeeId,
                    'description' => 'Job order picked for Order #' . $order->order_id
                ]);
            }

            // Mark order as picked and change status to "In Progress" (status stays if already In Progress)
            if ($order->status === 'Pending') {
                $order->status = 'In Progress';
            }
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job order picked! Inventory reduced and job order created.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Pick Job Order Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to pick job order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Done Job Order
     * Updates job order records and changes order status to "Released"
     * NO INVENTORY CHANGES - only status update and job order completion
     */
    public function doneJobOrder(Request $request, $orderId)
    {
        try {
            DB::beginTransaction();

            $employeeId = auth()->user()->employee->employee_id;

            // Find order and verify it's assigned to this employee
            $order = Order::with('items')
                ->where('order_id', $orderId)
                ->where('assigned_to', $employeeId)
                ->firstOrFail();

            // Check if order has been picked by checking if job orders exist
            $hasJobOrders = Joborder::whereIn('orderdetails_id', $order->items->pluck('orderdetails_id'))
                ->where('made_by', $employeeId)
                ->exists();

            if (!$hasJobOrders) {
                throw new \Exception('You must pick the job order first before marking it as done.');
            }

            // Check if order is already released
            if ($order->status === 'Released') {
                throw new \Exception('Order is already released.');
            }

            // Update job order entries to "Completed"
            foreach ($order->items as $item) {
                Joborder::where('orderdetails_id', $item->orderdetails_id)
                    ->where('made_by', $employeeId)
                    ->whereNull('joborder_end')
                    ->update([
                        'status' => 'Completed',
                        'joborder_end' => now()
                    ]);
            }

            // Update order status to "Released"
            $order->status = 'Released';
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job order completed! Order is now "Released" and ready for pickup.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Done Job Order Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark job order as done: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($joborderId)
    {
        $employeeId = auth()->user()->employee->employee_id;

        $jobOrder = Joborder::with([
            'orderDetail.order.customer',
            'orderDetail.stock.product',
            'employee'
        ])
        ->where('joborder_id', $joborderId)
        ->where('made_by', $employeeId)
        ->firstOrFail();

        return response()->json([
            'success' => true,
            'jobOrder' => $jobOrder
        ]);
    }
}
