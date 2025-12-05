<?php

namespace App\Http\Controllers;

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
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'category_id' => 'required|exists:categories,category_id',
                'order_date' => 'required|date',
                'product_type' => 'required|string|in:stockin_id,deliverydetails_id',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => 'required|exists:inventory,stock_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.color' => 'nullable|string',
                'items.*.size' => 'nullable|string',
                // total_amount is expected to be VAT-INCLUSIVE (subtotal + VAT)
                'total_amount' => 'required|numeric|min:0',
                // Optional VAT fields coming from frontend
                'vat_percent' => 'nullable|numeric|min:0',
                'vat_amount' => 'nullable|numeric|min:0',
                'cash' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:Cash,GCash',
                'reference_number' => 'nullable|string',
            ]);

            DB::transaction(function() use ($validated, &$order, &$payment) {

                $vatPercent        = isset($validated['vat_percent']) ? (float)$validated['vat_percent'] : 0.0;
                $providedVatAmount = isset($validated['vat_amount']) ? (float)$validated['vat_amount'] : null;

                $calculatedSubtotal = 0.0;
                foreach ($validated['items'] as $it) {
                    $calculatedSubtotal += ((float)$it['price'] * (int)$it['quantity']);
                }
                $calculatedSubtotal = round($calculatedSubtotal, 2);

                $calculatedVatTotal  = round($calculatedSubtotal * ($vatPercent / 100.0), 2);
                $calculatedGrandTotal = round($calculatedSubtotal + $calculatedVatTotal, 2);

                $clientTotal = round((float)$validated['total_amount'], 2);

                if (abs($clientTotal - $calculatedGrandTotal) > 0.01) {
                    throw new \Exception("Total mismatch. Client total ({$clientTotal}) does not equal computed subtotal + VAT ({$calculatedGrandTotal}).");
                }

                $amount = $clientTotal; 
                $cash   = (float)$validated['cash'];
                $change_amount = max($cash - $amount, 0);
                $balance       = max($amount - $cash, 0);

                if ($validated['product_type'] === 'stockin_id' && $cash < $amount) {
                    throw new \Exception('Full payment is required for Ready Made products!');
                }

                $paymentStatus = ($balance > 0) ? 'Partial' : 'Fully Paid';

                $orderStatus = 'Pending';
                if ($validated['product_type'] === 'stockin_id' && $paymentStatus === 'Fully Paid') {
                    $orderStatus = 'Completed';
                }

                $firstStockId    = $validated['items'][0]['stock_id'];
                $firstInventory  = Inventory::with(['stockin', 'deliveryDetail'])->find($firstStockId);
                $orderProductTypeText = ($validated['product_type'] === 'stockin_id') ? 'Ready Made' : 'Customize Item';

                if ($firstInventory) {
                    if (!empty($firstInventory->product_type)) {
                        $orderProductTypeText = $firstInventory->product_type;
                    } else {
                        if ($validated['product_type'] === 'stockin_id'
                            && $firstInventory->stockin && !empty($firstInventory->stockin->type)) {
                            $orderProductTypeText = $firstInventory->stockin->type;
                        } elseif ($validated['product_type'] === 'deliverydetails_id'
                            && $firstInventory->deliveryDetail && !empty($firstInventory->deliveryDetail->type)) {
                            $orderProductTypeText = $firstInventory->deliveryDetail->type;
                        }
                    }
                }

                $order = Order::create([
                    'customer_id' => $validated['customer_id'],
                    'category_id' => $validated['category_id'],
                    'order_date'  => $validated['order_date'],
                    'ordered_by'  => auth()->user()->employee->employee_id,
                    'product_type'=> $orderProductTypeText,
                    'total_amount'=> $clientTotal,
                    'status'      => $orderStatus,
                ]);

                $accumVat = 0.0;

                // desired type based on select
                $selectedType = $validated['product_type'] === 'stockin_id' ? 'ready' : 'custom';

                foreach ($validated['items'] as $it) {
                    $stockId  = $it['stock_id'];
                    $quantity = (int)$it['quantity'];
                    $price    = (float)$it['price'];
                    $color    = $it['color'] ?? null;
                    $size     = $it['size'] ?? null;

                    $inventory = Inventory::with('product')->find($stockId);
                    if (!$inventory) {
                        throw new \Exception("Stock item not found: {$stockId}");
                    }

                    // NEW: validate type using product_type column
                    $stockType = $this->mapInventoryType($inventory->product_type);
                    if ($stockType !== $selectedType) {
                        $expectedText = $selectedType === 'custom' ? 'Customized item' : 'Ready Made item';
                        throw new \Exception("Selected product '{$inventory->product->product_name}' is not a {$expectedText}");
                    }

                    $rowTotal = round($price * $quantity, 2);
                    $rowVat   = round($rowTotal * ($vatPercent / 100.0), 2);
                    $accumVat += $rowVat;

                    OrderDetail::create([
                        'order_id' => $order->order_id,
                        'stock_id' => $stockId,
                        'color'    => $color,
                        'size'     => $size,
                        'quantity' => $quantity,
                        'price'    => $price,
                        'vat'      => $rowVat,
                    ]);

                    // deduct stock only for ready-made
                    if ($validated['product_type'] === 'stockin_id') {
                        if ($inventory->current_stock < $quantity) {
                            throw new \Exception("Insufficient stock for: {$inventory->product->product_name}. Available: {$inventory->current_stock}, Requested: {$quantity}");
                        }

                        $inventory->current_stock -= $quantity;
                        $inventory->last_updated   = now();
                        $inventory->save();

                        Stockout::create([
                            'stock_id'    => $stockId,
                            'employee_id' => auth()->user()->employee->employee_id,
                            'quantity_out'=> $quantity,
                            'date_out'    => now(),
                            'reason'      => 'Order #' . $order->order_id . ' - Customer: ' . $order->customer->fname . ' ' . $order->customer->lname,
                            'status'      => 'Completed',
                            'approved_by' => null,
                        ]);
                    }
                }

                $accumVat = round($accumVat, 2);

                // If client provided vat_amount, ensure it matches accumulated per-row VAT (tolerance 0.01)
                if ($providedVatAmount !== null && abs($providedVatAmount - $accumVat) > 0.01) {
                    throw new \Exception("VAT mismatch. Provided VAT ({$providedVatAmount}) does not match computed VAT ({$accumVat}).");
                }

                $payment = Payment::create([
                    'order_id'       => $order->order_id,
                    'employee_id'    => auth()->user()->employee->employee_id,
                    'payment_date'   => $validated['order_date'],
                    'amount'         => $amount,
                    'cash'           => $cash,
                    'change_amount'  => $change_amount,
                    'balance'        => $balance,
                    'status'         => $paymentStatus,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'],
                ]);

                // done transaction
            });

            return response()->json([
                'success' => true,
                'message' => 'Order and payment created successfully',
                'order'   => $order->load('items', 'customer', 'payments'),
                'payment' => $payment,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
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

            $order = Order::with('items')->find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }

            // FOR CUSTOMIZED ITEMS: Deduct inventory when marking as completed
            if ($order->product_type === 'deliverydetails_id') {
                foreach ($order->items as $orderDetail) {
                    $inventory = Inventory::find($orderDetail->stock_id);
                    
                    if (!$inventory) {
                        throw new \Exception("Stock item not found: {$orderDetail->stock_id}");
                    }

                    if ($inventory->current_stock < $orderDetail->quantity) {
                        throw new \Exception("Insufficient stock for: {$inventory->product->product_name}. Available: {$inventory->current_stock}, Required: {$orderDetail->quantity}");
                    }

                    $inventory->current_stock -= $orderDetail->quantity;
                    $inventory->last_updated   = now();
                    $inventory->save();

                    Stockout::create([
                        'stock_id'    => $orderDetail->stock_id,
                        'employee_id' => auth()->user()->employee->employee_id,
                        'quantity_out'=> $orderDetail->quantity,
                        'date_out'    => now(),
                        'reason'      => 'Customized Order #' . $order->order_id . ' Completed - Customer: ' . $order->customer->fname . ' ' . $order->customer->lname,
                        'status'      => 'Completed',
                        'approved_by' => null,
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
            \Log::error('Mark as Completed Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'order_id'        => 'required|exists:orders,order_id',
            'cash'            => 'required|numeric|min:0',
            'balance'         => 'required|numeric|min:0',
            'status'          => 'required|in:Fully Paid,Partial',
            'change_amount'   => 'required|numeric|min:0',
            'payment_method'  => 'required|in:Cash,GCash',
            'reference_number'=> 'nullable|string|max:255'
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
                'cash'            => $validated['cash'],
                'balance'         => $validated['balance'],
                'status'          => $validated['status'],
                'change_amount'   => $validated['change_amount'],
                'payment_method'  => $validated['payment_method'],
                'reference_number'=> $validated['reference_number'],
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
                ->where('role_id', 4)
                ->orderBy('status', 'desc')
                ->orderBy('fname', 'asc')
                ->get();

            \Log::info('Layout Artists fetched for job assignment:', [
                'total'    => $employees->count(),
                'active'   => $employees->where('status', 'Active')->count(),
                'inactive' => $employees->where('status', 'Inactive')->count(),
                'role_id'  => 4
            ]);

            return response()->json([
                'success' => true,
                'employees' => $employees,
                'total' => $employees->count()
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching Layout Artist employees:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load employees: ' . $e->getMessage(),
                'employees' => []
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
}
