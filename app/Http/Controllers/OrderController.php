<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Employee; 
use App\Models\OrderDetail;
use App\Models\Stockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // helper: normalize inventory.product_type to 'ready' | 'custom'
    private function mapInventoryType(?string $raw): string
    {
        if (!$raw) return '';
        $s = strtolower(trim($raw));

        // match your DB values
        if ($s === 'ready made')     return 'ready';
        if ($s === 'customize item') return 'custom';

        // extra safety
        if (str_contains($s, 'ready'))  return 'ready';
        if (str_contains($s, 'custom')) return 'custom';

        return '';
    }

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
        
        $inventories = Inventory::with([
            'product',
            'stockin',
            'deliveryDetail'
        ])->get();
        
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
        $customers   = Customer::all();
        $inventories = Inventory::with('product')->get();
        $categories  = Category::all();

        return view('managestore.order-create', compact(
            'customers',
            'inventories',
            'categories',
        ));
    }

   public function store(Request $request)
{
    try {
        Log::info('Order store request received', $request->all());

        // Validate the request
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,customer_id',
            'order_date'        => 'required|date',
            'product_type'      => 'required|string|in:stockin_id,deliverydetails_id',
            'items'             => 'required|array|min:1',
            'items.*.stock_id'  => 'required|exists:inventory,stock_id',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.price'     => 'required|numeric|min:0',
            'items.*.custom_amount' => 'nullable|numeric|min:0',
            'items.*.profit'        => 'required|numeric',
            'items.*.color'         => 'nullable|string',
            'items.*.size'          => 'nullable|string',
            'total_amount'      => 'required|numeric|min:0',
            'cash'              => 'required|numeric|min:0',
            'payment_method'    => 'required|string|in:Cash,GCash',
            'reference_number'  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Get authenticated user's employee ID safely
            $employeeId = null;
            if (auth()->check() && auth()->user()->employee) {
                $employeeId = auth()->user()->employee->employee_id;
            }

            // Get first item's inventory for category and type checking
            $firstStockId = $validated['items'][0]['stock_id'];
            $firstInventory = Inventory::with(['product.category', 'stockin', 'deliveryDetail'])
                ->find($firstStockId);
            
            if (!$firstInventory) {
                throw new \Exception("Stock item not found: {$firstStockId}");
            }

            if (!$firstInventory->product) {
                throw new \Exception("Product not found for stock item: {$firstStockId}");
            }

            $categoryId = $firstInventory->product->category_id;

            // Recompute totals from items for verification
            $computedTotal = 0.0;
            $computedProfit = 0.0;

            foreach ($validated['items'] as $it) {
                $qty = (int) $it['quantity'];
                $unitPrice = (float) $it['price'];
                $lineCustom = (float) ($it['custom_amount'] ?? 0);
                $profitPerUnit = (float) $it['profit'];

                $lineTotal = $unitPrice * $qty + $lineCustom;
                $lineProfit = $profitPerUnit * $qty + $lineCustom;

                $computedTotal += $lineTotal;
                $computedProfit += $lineProfit;
            }

            $computedTotal = round($computedTotal, 2);
            $computedProfit = round($computedProfit, 2);
            $clientTotal = round((float) $validated['total_amount'], 2);

            Log::info('Total calculation', [
                'client_total' => $clientTotal,
                'computed_total' => $computedTotal,
                'items' => $validated['items']
            ]);

            // Payment logic
            $amount = $clientTotal;
            $cash = (float) $validated['cash'];
            $change_amount = max($cash - $amount, 0);
            $balance = max($amount - $cash, 0);

            // Validate payment for ready-made products
            if ($validated['product_type'] === 'stockin_id' && $cash < $amount) {
                throw new \Exception('Full payment is required for Ready Made products!');
            }

            $paymentStatus = ($balance > 0) ? 'Partial' : 'Fully Paid';

            // Determine product type text
            $orderProductTypeText = ($validated['product_type'] === 'stockin_id')
                ? 'Ready Made'
                : 'Customize Item';

            // Try to get more specific product type from inventory
            if ($firstInventory) {
                if (!empty($firstInventory->product_type)) {
                    $orderProductTypeText = $firstInventory->product_type;
                }
            }

            // Determine order status
            $orderStatus = 'Pending';
            if ($validated['product_type'] === 'stockin_id' && $paymentStatus === 'Fully Paid') {
                $orderStatus = 'Completed';
            }

            // Create Order
            $orderData = [
                'customer_id'   => $validated['customer_id'],
                'category_id'   => $categoryId,
                'order_date'    => $validated['order_date'],
                'ordered_by'    => $employeeId,
                'product_type'  => $orderProductTypeText,
                'total_amount'  => $amount,
                'status'        => $orderStatus,
            ];
            
            Log::info('Creating order with data:', $orderData);
            $order = Order::create($orderData);
            
            if (!$order) {
                throw new \Exception('Failed to create order');
            }
            Log::info('Order created successfully with ID: ' . $order->order_id);

            // Determine expected stock type
            $selectedType = $validated['product_type'] === 'stockin_id' ? 'ready' : 'custom';

            // Process each item
            foreach ($validated['items'] as $index => $it) {
                $stockId = $it['stock_id'];
                $quantity = (int) $it['quantity'];
                $price = (float) $it['price'];
                $color = $it['color'] ?? null;
                $size = $it['size'] ?? null;
                $custom_amount = (float) ($it['custom_amount'] ?? 0);
                $profitPerUnit = (float) $it['profit'];

                // Reload inventory for each item to ensure fresh data
                $inventory = Inventory::with('product')->find($stockId);
                
                if (!$inventory) {
                    throw new \Exception("Stock item not found: {$stockId}");
                }

                // Verify stock type matches
                $stockType = $this->mapInventoryType($inventory->product_type);
                if ($stockType !== $selectedType) {
                    $expectedText = $selectedType === 'custom' ? 'Customized item' : 'Ready Made item';
                    $actualText = $inventory->product_type ?? 'unknown';
                    throw new \Exception("Selected product '{$inventory->product->product_name}' is a '{$actualText}' but expected a {$expectedText}");
                }

                $lineProfit = $profitPerUnit * $quantity + $custom_amount;

                // Create OrderDetail
                $orderDetailData = [
                    'order_id'      => $order->order_id,
                    'stock_id'      => $stockId,
                    'color'         => $color,
                    'size'          => $size,
                    'quantity'      => $quantity,
                    'price'         => $price,
                    'custom_amount' => $custom_amount,
                    'profit'        => $lineProfit,
                ];
                
                Log::info('Creating order detail:', $orderDetailData);
                $orderDetail = OrderDetail::create($orderDetailData);
                
                if (!$orderDetail) {
                    throw new \Exception("Failed to create order detail for item {$index}");
                }

                // For Ready Made: update inventory and create stockout
                if ($validated['product_type'] === 'stockin_id') {
                    if ($inventory->current_stock < $quantity) {
                        throw new \Exception("Insufficient stock for: {$inventory->product->product_name}. Available: {$inventory->current_stock}, Requested: {$quantity}");
                    }

                    // Update inventory
                    $inventory->current_stock -= $quantity;
                    $inventory->last_updated = now();
                    $inventory->save();

                    // Create stockout record
                    $customer = Customer::find($validated['customer_id']);
                    $customerName = $customer ? ($customer->fname . ' ' . $customer->lname) : 'Unknown';

                    $stockoutData = [
                        'stock_id'      => $stockId,
                        'employee_id'   => $employeeId,
                        'quantity_out'  => $quantity,
                        'date_out'      => now(),
                        'reason'        => 'Order #' . $order->order_id . ' - Customer: ' . $customerName,
                        'status'        => 'Completed',
                        'approved_by'   => null,
                        'product_type'  => $inventory->product_type ?? $orderProductTypeText,
                        'size'          => $size,
                    ];
                    
                    Log::info('Creating stockout:', $stockoutData);
                    $stockout = Stockout::create($stockoutData);
                    
                    if (!$stockout) {
                        throw new \Exception("Failed to create stockout record for item {$index}");
                    }
                }
            }

            // Create Payment
            $paymentData = [
                'order_id'        => $order->order_id,
                'employee_id'     => $employeeId,
                'payment_date'    => $validated['order_date'],
                'amount'          => $amount,
                'cash'            => $cash,
                'change_amount'   => $change_amount,
                'balance'         => $balance,
                'status'          => $paymentStatus,
                'payment_method'  => $validated['payment_method'],
                'reference_number'=> $validated['reference_number'] ?? null,
                'profit'          => $computedProfit,
            ];
            
            Log::info('Creating payment:', $paymentData);
            $payment = Payment::create($paymentData);
            
            if (!$payment) {
                throw new \Exception('Failed to create payment record');
            }

            DB::commit();
            Log::info('Order transaction completed successfully');

            // Load relationships for response
            $order->load(['customer', 'items', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Order and payment created successfully',
                'order' => $order,
                'payment' => $payment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            throw $e; // Re-throw to be caught by outer catch
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . json_encode($e->errors()),
            'errors'  => $e->errors(),
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Order Store Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => 'Check storage/logs/laravel.log for details',
        ], 500);
    }
}

    // ... [REST OF YOUR METHODS STAY EXACTLY THE SAME - storeCustomer, markAsCompleted, etc.]
    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'fname'      => 'required|string|max:255',
            'mname'      => 'nullable|string|max:255',
            'lname'      => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success'  => true,
            'customer' => $customer
        ]);
    }

   public function markAsCompleted($orderId)
{
    try {
        DB::beginTransaction();

        // FIX: Load customer relationship
        $order = Order::with(['items.inventory.product', 'customer'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        // Get employee ID safely
        $employeeId = null;
        if (auth()->check() && auth()->user()->employee) {
            $employeeId = auth()->user()->employee->employee_id;
        }

        // FOR CUSTOMIZED ITEMS: Deduct inventory when marking as completed
        if ($order->product_type === 'deliverydetails_id' || $order->product_type === 'Customize Item') {
            foreach ($order->items as $orderDetail) {
                $inventory = Inventory::find($orderDetail->stock_id);
                
                if (!$inventory) {
                    throw new \Exception("Stock item not found: {$orderDetail->stock_id}");
                }

                if ($inventory->current_stock < $orderDetail->quantity) {
                    throw new \Exception("Insufficient stock for item. Available: {$inventory->current_stock}, Required: {$orderDetail->quantity}");
                }

                $inventory->current_stock -= $orderDetail->quantity;
                $inventory->last_updated = now();
                $inventory->save();

                $customerName = $order->customer ? ($order->customer->fname . ' ' . $order->customer->lname) : 'Unknown';

                Stockout::create([
                    'stock_id'      => $orderDetail->stock_id,
                    'employee_id'   => $employeeId,
                    'quantity_out'  => $orderDetail->quantity,
                    'date_out'      => now(),
                    'reason'        => 'Customized Order #' . $order->order_id . ' Completed - Customer: ' . $customerName,
                    'status'        => 'Completed',
                    'approved_by'   => null,
                    'product_type'  => $inventory->product_type ?? 'Customize Item',
                    'size'          => $orderDetail->size,
                ]);
            }
        }

        $order->status = 'Completed';
        $order->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Order marked as Completed.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Mark as Completed Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'order_id' => $orderId
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'order_id'          => 'required|exists:orders,order_id',
            'cash'              => 'required|numeric|min:0',
            'balance'           => 'required|numeric|min:0',
            'status'            => 'required|in:Fully Paid,Partial',
            'change_amount'     => 'required|numeric|min:0',
            'payment_method'    => 'required|in:Cash,GCash',
            'reference_number'  => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::where('order_id', $validated['order_id'])->first();

            if (!$payment) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found.',
                ], 404);
            }

            // Update payment
            $payment->update([
                'cash'             => $validated['cash'],
                'balance'          => $validated['balance'],
                'status'           => $validated['status'],
                'change_amount'    => $validated['change_amount'],
                'payment_method'   => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
            ]);

            // Reload with fresh values
            $payment->refresh();

            // Load order + customer + items (+ inventory.product for product_name)
            $order = Order::with([
                'customer',
                'items' => function ($q) {
                    $q->with('inventory.product');
                },
            ])->findOrFail($validated['order_id']);

            // Flatten order items for the receipt
            $receiptItems = $order->items->map(function ($od) {
                // Prefer stored product_name, else from related product
                $productName = $od->product_name;
                if (!$productName && $od->inventory && $od->inventory->product) {
                    $productName = $od->inventory->product->product_name;
                }

                $unitPrice    = (float)$od->price;
                $customAmount = (float)($od->custom_amount ?? 0);
                $qty          = (int)$od->quantity;
                $lineTotal    = $unitPrice * $qty + $customAmount;

                return [
                    'quantity'      => $qty,
                    'product_name'  => $productName,
                    'size'          => $od->size,
                    'color'         => $od->color,
                    'unit_price'    => $unitPrice,
                    'custom_amount' => $customAmount,
                    'amount'        => $lineTotal,
                ];
            })->values();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'Fully Paid'
                    ? 'Payment completed successfully!'
                    : 'Payment updated. Remaining balance: ₱' . number_format($validated['balance'], 2),
                'payment' => $payment,
                'order'   => [
                    'customer_name'    => $order->customer
                        ? $order->customer->fname . ' ' . $order->customer->lname
                        : '',
                    'customer_address' => $order->customer->address ?? '',
                    'items'            => $receiptItems,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActiveEmployees()
    {
        try {
            $employees = Employee::select(
                    'employee_id',
                    'role_id',
                    'fname',
                    'lname',
                    'status',
                    'email',
                    'contact_no'
                )
                ->whereIn('role_id', [3, 4]) // allow both roles
                ->orderBy('status', 'desc')
                ->orderBy('fname', 'asc')
                ->get();

            \Log::info('Employees fetched for job assignment (roles 3 & 4):', [
                'total'     => $employees->count(),
                'active'    => $employees->where('status', 'Active')->count(),
                'inactive'  => $employees->where('status', 'Inactive')->count(),
                'role_ids'  => [3, 4],
            ]);

            return response()->json([
                'success'   => true,
                'employees' => $employees,
                'total'     => $employees->count(),
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching employees for job assignment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success'   => false,
                'message'   => 'Failed to load employees: ' . $e->getMessage(),
                'employees' => [],
            ], 500);
        }
    }


    public function assignJobOrder(Request $request)
    {
        $validated = $request->validate([
            'order_id'  => 'required|exists:orders,order_id',
            'employees' => 'required|array|min:1',
            'employees.*' => 'exists:employees,employee_id',
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);

            $order->assigned_to = $validated['employees'][0];
            $order->status      = 'In Progress';
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Job Order assigned successfully!',
                'assigned_to' => $order->assigned_to
            ]);
        } catch (\Exception $e) {
            \Log::error('Assign Job Order Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign Job Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Payment $payment)
    {
        $order = $payment->order()->with(['customer', 'items.inventory.product'])->first();

        $items = $order->items->map(function ($od) {
            $name = $od->product_name ?? optional(optional($od->inventory)->product)->product_name;
            $unit = (float) $od->price;
            $custom = (float) ($od->custom_amount ?? 0);
            $qty = (int) $od->quantity;
            return [
                'quantity'      => $qty,
                'product_name'  => $name,
                'size'          => $od->size,
                'color'         => $od->color,
                'unit_price'    => $unit,
                'custom_amount' => $custom,
                'amount'        => $unit * $qty + $custom,
            ];
        });

        return response()->json([
            'payment' => $payment,
            'order'   => [
                'amount'           => (float) $order->total_amount,
                'customer_name'    => $order->customer ? $order->customer->fname.' '.$order->customer->lname : '',
                'customer_address' => $order->customer->address ?? '',
                'items'            => $items,
            ],
        ]);
    }

}
