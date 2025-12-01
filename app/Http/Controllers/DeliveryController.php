<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    // Show delivery page
    public function index()
    {
        // Eager load supplier, employee, and delivery details with product info
        $deliveries = Delivery::with(['supplier', 'employee', 'details.product'])->get();

        // Pass suppliers and products for Add Delivery modal
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('maincontent.delivery', compact('deliveries', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'delivery_date_request' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.product_type' => 'required|string|in:Ready Made,Customize Item',
            'products.*.quantity_product' => 'required|integer|min:1',
            'products.*.unit' => 'nullable|string|max:50',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'products.*.size' => 'nullable|string|max:50',
        ]);

        $employeeId = auth()->user()->employee->employee_id;

        try {
            DB::beginTransaction();

            // Create Delivery
            $delivery = Delivery::create([
                'supplier_id' => $validated['supplier_id'],
                'employee_id' => $employeeId,
                'delivery_date_request' => $validated['delivery_date_request'],
                'status' => 'Pending',
                'delivery_date_received' => null,
                'received_by' => null,
            ]);

            // Create Delivery Details
            foreach ($validated['products'] as $item) {
                $unit = $item['unit'] ?? Product::find($item['product_id'])->unit ?? 'pcs';
                $total = $item['quantity_product'] * $item['unit_cost'];

                DeliveryDetail::create([
                    'delivery_id' => $delivery->delivery_id,
                    'product_id' => $item['product_id'],
                    'product_type' => $item['product_type'], // ✅ Saved to delivery_details
                    'quantity_product' => $item['quantity_product'],
                    'unit' => $unit,
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                    'size' => $item['size'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('delivery')
                ->with('success', 'Delivery created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create delivery: ' . $e->getMessage());
        }
    }

    // Update delivery status
    public function update(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);

        $delivery->status = $request->status;

        // If status is Delivered, set received date if empty
        if ($request->status === 'Delivered' && !$delivery->delivery_date_received) {
            $delivery->delivery_date_received = now()->toDateString();
        }

        $delivery->save();

        return back()->with('success', 'Delivery status updated successfully.');
    }

  // Stock in a delivery
public function stockIn(Request $request, $delivery_id)
{
    try {
        DB::beginTransaction();

        $delivery = Delivery::with('details')->findOrFail($delivery_id);
        $employeeId = auth()->user()->employee->employee_id;

        // Update delivery: mark as delivered and set received_by/date
        $delivery->update([
            'received_by' => $employeeId,
            'delivery_date_received' => Carbon::now()->toDateString(),
            'status' => 'Delivered',
        ]);

        // Insert into inventory for each delivery detail
        foreach ($delivery->details as $detail) {
            Inventory::create([
                'deliverydetails_id' => $detail->deliverydetails_id,
                'product_id' => $detail->product_id,
                'total_stock' => $detail->quantity_product,
                'current_stock' => $detail->quantity_product,
                'unit_cost' => $detail->unit_cost,
                'date_received' => Carbon::now()->toDateString(),
                'received_by' => $employeeId,
                'last_updated' => now(),
                'size' => $detail->size,
                'product_type' => $detail->product_type ?? 'Ready Made', // use the actual type from detail
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Delivery successfully stocked in and inventory updated.',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to stock in delivery: ' . $e->getMessage()
        ], 500);
    }
}


    // Show inventory page with delivered deliveries
    public function inventoryPage(Request $request)
    {
        $search = $request->get('search');

        $deliveryHistory = Delivery::where('status', 'Delivered')
            ->where(function ($query) use ($search) {
                $query->where('delivery_id', 'LIKE', "%$search%")
                      ->orWhereHas('employee', function ($q) use ($search) {
                          $q->where('fname', 'LIKE', "%$search%")
                            ->orWhere('lname', 'LIKE', "%$search%");
                      })
                      ->orWhereHas('supplier', function ($q) use ($search) {
                          $q->where('supplier_name', 'LIKE', "%$search%");
                      });
            })
            ->paginate(10);

        return view('inventory.index', compact('deliveryHistory'));
    }
}