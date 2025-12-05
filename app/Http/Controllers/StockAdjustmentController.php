<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Models\Inventory;
use App\Models\Stockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockAdjustmentController extends Controller
{
    // Show all stock adjustments
    public function index()
    {
        $adjustments = StockAdjustment::with([
            'stock.product',
            'employee',
            'adjustedBy',
            'approvedBy'
        ])->orderBy('created_at', 'desc')->get();

        // Get all available stocks and group by product + type + size
        $stocksRaw = Inventory::with('product')
            ->where('current_stock', '>', 0)
            ->get();

        $stocks = $stocksRaw
            ->groupBy(function ($item) {
                return $item->product_id . '|' . $item->product_type . '|' . $item->size;
            })
            ->map(function ($group) {
                $first = $group->first();

                return (object) [
                    // representative stock id (first in group)
                    'stock_id'      => $first->stock_id,
                    // ALL stock_ids in this group (used in data-stock-ids)
                    'stock_ids'     => $group->pluck('stock_id')->toArray(),
                    'product'       => $first->product,
                    'product_type'  => $first->product_type,
                    'size'          => $first->size,
                    // total of current_stock for all rows in group
                    'current_stock' => $group->sum('current_stock'),
                ];
            })
            ->values(); // reset keys for foreach

        return view('inventory.stockadjustment', compact('adjustments', 'stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // still validating against inventory.stock_id for now
            'stock_group_key'    => 'required',
            'adjustment_type'    => 'required|in:Addition,Deduction,Correction',
            'quantity_adjusted'  => 'required|integer|min:1',
            'reason'             => 'required|string|max:500',
            'request_date'       => 'required|date',
            // optional: stock_ids (JSON string) if you want to use it later
        ]);

        $employeeId = Auth::user()->employee_id;

        // use representative stock id for now
        $stockId = $request->input('stock_group_key');

        $adjustment = StockAdjustment::create([
            'stock_id'          => $stockId,
            'employee_id'       => $employeeId,
            'adjustment_type'   => $request->adjustment_type,
            'quantity_adjusted' => $request->quantity_adjusted,
            'request_date'      => $request->request_date,
            'reason'            => $request->reason,
            'status'            => 'Pending',
            'adjusted_by'       => $employeeId,
            'approved_by'       => null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment request submitted successfully! Waiting for approval.',
            ]);
        }

        return redirect()->back()->with('success', 'Stock adjustment request submitted successfully!');
    }

    // Approve stock adjustment
    public function approve($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        if ($adjustment->status !== 'Pending') {
            return redirect()->back()->with('error', 'This adjustment has already been processed.');
        }

        $employeeId = Auth::user()->employee_id;

        DB::transaction(function () use ($adjustment, $employeeId) {
            $stock = Inventory::findOrFail($adjustment->stock_id);

            switch ($adjustment->adjustment_type) {
                case 'Addition':
                    $stock->current_stock += $adjustment->quantity_adjusted;
                    $stock->total_stock   += $adjustment->quantity_adjusted;
                    break;

                case 'Deduction':
                    $newStock = $stock->current_stock - $adjustment->quantity_adjusted;
                    if ($newStock < 0) {
                        throw new \Exception('Cannot deduct more than current stock.');
                    }
                    $stock->current_stock = $newStock;

                    Stockout::create([
                        'stock_id'     => $stock->stock_id,
                        'employee_id'  => $employeeId,
                        'quantity_out' => $adjustment->quantity_adjusted,
                        'date_out'     => now(),
                        'reason'       => 'Stock Adjustment Deduction - ' . $adjustment->reason,
                        'size'         => $stock->size ?? null,
                        'status'       => 'Deducted',
                        'approved_by'  => $employeeId,
                    ]);
                    break;

                case 'Correction':
                    $stock->current_stock = $adjustment->quantity_adjusted;
                    break;
            }

            $stock->last_updated = now();
            $stock->save();

            $adjustment->update([
                'status'      => 'Approved',
                'adjusted_by' => $employeeId,
                'approved_by' => $employeeId,
            ]);
        });

        return redirect()->back()->with('success', 'Stock adjustment approved and inventory updated!');
    }

    // Reject stock adjustment
    public function reject($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        if ($adjustment->status !== 'Pending') {
            return redirect()->back()->with('error', 'This adjustment has already been processed.');
        }

        $employeeId = Auth::user()->employee_id;

        $adjustment->update([
            'status'      => 'Rejected',
            'approved_by' => $employeeId,
        ]);

        return redirect()->back()->with('success', 'Stock adjustment rejected.');
    }

    // Approve stock adjustment via AJAX (for Request page)
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
            $adjustment->status      = 'Rejected';
            $adjustment->approved_by = Auth::user()->employee_id;
            $adjustment->save();

            return response()->json([
                'success' => true,
                'message' => 'Adjustment rejected successfully.',
            ]);
        }

        DB::transaction(function () use ($adjustment) {
            $stock = $adjustment->stock;

            if (!$stock) {
                throw new \Exception('Related stock record not found.');
            }

            $qty        = (int) $adjustment->quantity_adjusted;
            $type       = $adjustment->adjustment_type;
            $employeeId = Auth::user()->employee_id;

            $current = (int) $stock->current_stock;
            $total   = (int) $stock->total_stock;

            if ($type === 'Addition') {
                $current += $qty;
                $total   += $qty;
            } elseif ($type === 'Deduction') {
                $current -= $qty;
                $total   -= $qty;
                if ($current < 0) $current = 0;
                if ($total   < 0) $total   = 0;

                Stockout::create([
                    'stock_id'     => $stock->stock_id,
                    'employee_id'  => $employeeId,
                    'quantity_out' => $qty,
                    'date_out'     => now(),
                    'reason'       => 'Stock Adjustment Deduction - ' . $adjustment->reason,
                    'size'         => $stock->size ?? null,
                    'status'       => 'Deducted',
                    'approved_by'  => $employeeId,
                ]);
            } elseif ($type === 'Correction') {
                $current = $qty;
            }

            $stock->current_stock = $current;
            $stock->total_stock   = $total;
            $stock->last_updated  = now();
            $stock->save();

            $adjustment->status      = 'Approved';
            $adjustment->approved_by = $employeeId;
            $adjustment->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment approved, inventory updated, and stock out record created.',
        ]);
    }
}
