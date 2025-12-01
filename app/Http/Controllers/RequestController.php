<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
        public function index()
    {
        $deliveries = Delivery::with(['supplier', 'employee', 'receiver', 'details', 'details.product'])->get();

        $stockAdjustments = StockAdjustment::with([
                'stock.product',
                'adjustedBy',
            ])
            ->whereNull('approved_by')
            ->get();

        $pendingCount = $stockAdjustments->count();

        return view('maincontent.request', compact('deliveries', 'stockAdjustments', 'pendingCount'));
    }


    public function updateStatus(Request $request, $deliveryId)
    {
        $request->validate([
            'status' => 'required|string|in:Out for Delivery,For Stock In,Delivered',
        ]);

        $delivery = Delivery::findOrFail($deliveryId);
        $delivery->status = $request->status;
        $delivery->save();

        return response()->json([
            'success' => true,
            'message' => "Delivery status updated to {$request->status}.",
            'status' => $delivery->status,
        ]);
    }

    // NEW: approve stock adjustment and update inventory
    public function approveStockAdjustment(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|string|in:accept,reject',
        ]);

        $adjustment = StockAdjustment::with('stock')->findOrFail($id);

        if ($adjustment->status === 'Approved') {
            return response()->json([
                'success' => false,
                'message' => 'This adjustment is already approved.',
            ], 422);
        }

        if ($request->action === 'reject') {
            $adjustment->status = 'Rejected';
            $adjustment->approved_by = Auth::id();
            $adjustment->save();

            return response()->json([
                'success' => true,
                'message' => 'Adjustment rejected successfully.',
            ]);
        }

        // accept / approve
        DB::transaction(function () use ($adjustment) {
            $stock = $adjustment->stock;

            if (!$stock) {
                throw new \Exception('Related stock record not found.');
            }

            $qty = (int) $adjustment->quantity_adjusted;
            $type = $adjustment->adjustment_type; // 'Addition', 'Deduction', 'Correction'

            // current values
            $current = (int) $stock->current_stock;
            $total   = (int) $stock->total_stock;

            if ($type === 'Addition') {
                $current += $qty;
                $total   += $qty;
            } elseif ($type === 'Deduction') {
                $current -= $qty;
                $total   -= $qty;
                if ($current < 0) $current = 0;
                if ($total < 0) $total = 0;
            } elseif ($type === 'Correction') {
                // no change to stock numbers
            }

            $stock->current_stock = $current;
            $stock->total_stock   = $total;
            $stock->save();

            $adjustment->status      = 'Approved';
            $adjustment->approved_by = Auth::id(); // user who approved
            $adjustment->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment approved and inventory updated.',
        ]);
    }
}
