<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Models\Inventory;
use App\Models\Product;
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

        // Get all available stocks for the dropdown
        $stocks = Inventory::with('product')
            ->where('current_stock', '>', 0)
            ->get();

        return view('inventory.stockadjustment', compact('adjustments', 'stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:inventory,stock_id',
            'adjustment_type' => 'required|in:Addition,Deduction,Correction',
            'quantity_adjusted' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'request_date' => 'required|date',
        ]);

        $employeeId = Auth::user()->employee_id;

        // Create the stock adjustment record
        $adjustment = StockAdjustment::create([
            'stock_id' => $request->stock_id,
            'employee_id' => $employeeId,          // requester
            'adjustment_type' => $request->adjustment_type,
            'quantity_adjusted' => $request->quantity_adjusted,
            'request_date' => $request->request_date,
            'reason' => $request->reason,
            'status' => 'Pending',
            'adjusted_by' => $employeeId,          // store logged-in employee here immediately
            'approved_by' => null,                 // still null, will be set in approve()
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
            // Update the stock based on adjustment type
            $stock = Inventory::findOrFail($adjustment->stock_id);

            switch ($adjustment->adjustment_type) {
                case 'Addition':
                    $stock->current_stock += $adjustment->quantity_adjusted;
                    $stock->total_stock += $adjustment->quantity_adjusted;
                    break;
                    
                case 'Deduction':
                    $newStock = $stock->current_stock - $adjustment->quantity_adjusted;
                    if ($newStock < 0) {
                        throw new \Exception('Cannot deduct more than current stock.');
                    }
                    $stock->current_stock = $newStock;
                    break;
                    
                case 'Correction':
                    // For correction, set the stock to the adjusted quantity
                    $stock->current_stock = $adjustment->quantity_adjusted;
                    break;
            }

            $stock->last_updated = now();
            $stock->save();

            // Update adjustment status
            $adjustment->update([
                'status' => 'Approved',
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
            'status' => 'Rejected',
            'approved_by' => $employeeId,
        ]);

        return redirect()->back()->with('success', 'Stock adjustment rejected.');
    }
}