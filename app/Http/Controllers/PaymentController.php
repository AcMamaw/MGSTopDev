<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        // Load payments with related orders and employees
        $payments = Payment::with(['order', 'employee'])->get();

        return view('maincontent.payment', compact('payments'));
    }
}
