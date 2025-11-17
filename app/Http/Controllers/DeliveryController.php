<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use Carbon\Carbon;

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
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'delivery_date_request' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.quantity_product' => 'required|integer|min:1',
            'products.*.unit' => 'nullable|string|max:50',
            'products.*.unit_cost' => 'required|numeric|min:0',
        ]);

        // Logged-in user employee
        $employeeId = auth()->user()->employee_id;

        // Create main delivery record
        $delivery = Delivery::create([
            'supplier_id' => $request->supplier_id,
            'employee_id' => $employeeId,
            'delivery_date_request' => $request->delivery_date_request,
            'status' => 'In Transit',
            'delivery_date_received' => null,
        ]);

        // Insert delivery items
        foreach ($request->products as $item) {
            DeliveryDetail::create([
                'delivery_id' => $delivery->delivery_id,
                'product_id' => $item['product_id'],
                'quantity_product' => $item['quantity_product'],
                'unit' => $item['unit'] ?? null,
                'unit_cost' => $item['unit_cost'],
                'total' => $item['quantity_product'] * $item['unit_cost'],
            ]);
        }

        // Redirect after transaction
        return redirect()
            ->route('delivery')
            ->with('success', 'Delivery created successfully!');
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

        return back()->with('success', 'Delivery updated.');
    }

    // -----------------------------
    // New: Stock In a Delivery
    // -----------------------------
  public function stockIn(Request $request, $delivery_id)
{
    $delivery = Delivery::with('details')->findOrFail($delivery_id);

    // Get logged-in employee
    $employeeId = auth()->user()->employee_id;

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
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Delivery successfully stocked in and inventory updated.',
    ]);
}


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
