<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display the request page.
     */
    public function index()
    {
        $deliveries = Delivery::with(['supplier', 'employee', 'receiver', 'details', 'details.product'])->get();
        return view('maincontent.request', compact('deliveries'));
    }

    /**
     * Update the delivery status via AJAX.
     */
    public function updateStatus(Request $request, $deliveryId)
    {
        // Validate input
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
}
