<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Eager load customer and order details with stock info
        $orders = Order::with(['customer', 'details.stock'])->get();

        return view('maincontent.purchaseorder', compact('orders'));
    }
}
