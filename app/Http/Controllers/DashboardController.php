<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\CarbonInterface;

class DashboardController extends Controller
{
    public function index()
    {
        // 1) Products that currently have stock (Top 10, unique per product)
        $availableProducts = Inventory::with('product')
            ->select(
                'product_id',
                DB::raw('SUM(current_stock) as total_stock'),
                DB::raw('MAX(product_type) as product_type'),
                DB::raw('GROUP_CONCAT(DISTINCT size ORDER BY size SEPARATOR ", ") as sizes')
            )
            ->groupBy('product_id')
            ->having('total_stock', '>', 0)
            ->orderByDesc('total_stock')
            ->take(10)
            ->get();

        // 2) Sales today (sum of payment amounts today)
        $salesToday = Payment::whereDate('payment_date', today())
            ->sum('amount');

        // 3) Low + medium stock counts
        $lowStockCount    = Inventory::whereBetween('current_stock', [1, 30])->count();
        $mediumStockCount = Inventory::whereBetween('current_stock', [31, 60])->count();
        $lowStockTotal    = $lowStockCount + $mediumStockCount;

        // 4) Sales per day (from last Sunday up to today) for chart
        $startOfWeek = now()->startOfWeek(CarbonInterface::SUNDAY);
        $endOfWeek   = now()->endOfDay();

        $salesByDay = Payment::select(
                DB::raw('DATE(payment_date) as day'),
                DB::raw('SUM(amount) as total')
            )
            ->whereBetween('payment_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // build labels for each day from Sunday → today, even if 0 sales
        $period = new \DatePeriod(
            $startOfWeek->copy(),
            new \DateInterval('P1D'),
            $endOfWeek->copy()->addDay() // include today
        );

        $chartLabels = [];
        $chartData   = [];

        $salesMap = $salesByDay->keyBy('day'); // map 'Y-m-d' => row

        foreach ($period as $date) {
            $dayStr        = $date->format('Y-m-d');
            $chartLabels[] = $date->format('D, M j'); // e.g. Sun, Dec 8
            $chartData[]   = isset($salesMap[$dayStr]) ? (float) $salesMap[$dayStr]->total : 0;
        }

        // 5) Low/medium stock products list (1–60)
        $lowStockProducts = Inventory::with('product')
            ->whereBetween('current_stock', [1, 60])
            ->orderBy('current_stock')
            ->get();

        // 6) Total Inventory Stock (Sum of all current_stock)
        $totalAvailableStock = Inventory::sum('current_stock');

        // 7) Transactions Today (Count of orders created today)
        $transactionsToday = Order::whereDate('order_date', today())->count();

        // 8) Monthly Sales (Total payments this month)
        $monthlySales = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // 9) Monthly Profit (assuming 'profit' column exists on payments)
        $monthlyProfit = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('profit');

        // 10) Open Orders (not Completed/Cancelled/Delivered)
        $openOrders = Order::whereNotIn('status', ['Completed', 'Cancelled', 'Delivered'])
            ->count();

        return view('maincontent.dashboard', [
            'availableProducts'   => $availableProducts,
            'salesToday'          => $salesToday,
            'lowStockTotal'       => $lowStockTotal,
            'lowStockProducts'    => $lowStockProducts,
            'chartLabels'         => $chartLabels,
            'chartData'           => $chartData,
            'totalAvailableStock' => $totalAvailableStock,
            'transactionsToday'   => $transactionsToday,
            'monthlySales'        => $monthlySales,
            'monthlyProfit'       => $monthlyProfit,
            'openOrders'          => $openOrders,
        ]);
    }
}
