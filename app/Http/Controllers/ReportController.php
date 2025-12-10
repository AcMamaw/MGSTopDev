<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use App\Models\Delivery;
use App\Models\Joborder;
use App\Models\Inventory;
use App\Models\StockAdjustment;
use App\Models\StockOut;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
     public function index()
    {
        // Report history list
        $reports = Report::with('generatedBy')
            ->orderByDesc('date_created')
            ->orderByDesc('report_id')
            ->paginate(20);

        // Data for each printable table
        $orderReports = Order::with(['customer', 'category'])
            ->orderByDesc('order_date')
            ->get();

        $deliveryReports = Delivery::with(['supplier', 'employee', 'receiver'])
            ->orderByDesc('delivery_date_request')
            ->get();

        $jobOrderReports = Joborder::with(['orderdetail.stock.product', 'employee'])
            ->orderByDesc('joborder_created')
            ->get();

        // Inventory now uses Inventory model
        $inventoryReports = Inventory::with(['product', 'receiver'])
            ->orderByDesc('stock_id')   
            ->get();

        $stockoutReports = StockOut::with(['stock.product', 'employee'])
            ->orderByDesc('date_out')
            ->get();

        $stockAdjustmentReports = StockAdjustment::with(['stock.product', 'employee'])
            ->orderByDesc('request_date')
            ->get();

        $paymentReports = Payment::with(['employee', 'order.customer'])
            ->orderByDesc('payment_date')
            ->get();

        return view('maincontent.reports', compact(
            'reports',
            'orderReports',
            'deliveryReports',
            'jobOrderReports',
            'inventoryReports',
            'stockoutReports',
            'stockAdjustmentReports',
            'paymentReports'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|string',
            'report_type' => 'required|string',
            'date_from'   => 'required|date',
            'date_to'     => 'required|date',
            'coverage'    => 'nullable|string',
        ]);

        $user = Auth::user();
        $emp  = $user?->employee;
        $generatedByEmployeeId = $emp?->employee_id;
        $generatedByName = 'System';

        if ($emp) {
            $generatedByName = trim(($emp->fname ?? '') . ' ' . ($emp->lname ?? '')) ?: 'System';
        }

        $ordersData    = [];
        $ids           = [];
        $stockIds      = []; // for inventory / stock-related categories
        $categoryLabel = $request->category;
        $coverage      = $request->coverage ?? 'N/A';

        switch ($request->category) {
            case 'Order':
                [$ordersData, $ids] = $this->buildOrderData($request);
                break;

            case 'Deliveries':
                [$ordersData, $ids] = $this->buildDeliveriesData($request);
                break;

            case 'Job Order':
                [$ordersData, $ids] = $this->buildJobOrderData($request);
                break;

            case 'Inventory':
                // returns [$data, $inventoryIds, $relatedStockIds]
                [$ordersData, $ids, $stockIds] = $this->buildInventoryData($request);
                break;

            case 'Stock Out':
                [$ordersData, $ids] = $this->buildStockOutData($request);
                break;

            case 'Stock Adjustment':
                [$ordersData, $ids] = $this->buildStockAdjustmentData($request);
                break;

            case 'Payment':
                [$ordersData, $ids] = $this->buildPaymentData($request);
                break;

            default:
                $ordersData = [];
                $ids        = [];
        }

        // Base payload for reports table
        $payload = [
            'payment_id'         => null,
            'stock_id'           => null,
            'order_id'           => null,
            'orderdetails_id'    => null,
            'delivery_id'        => null,
            'deliverydetails_id' => null,
            'joborder_id'        => null,
            'generated_by'       => $generatedByEmployeeId,
            'category'           => $categoryLabel,
            'report_type'        => $request->report_type,
            'coverage'           => $coverage,
            'date_created'       => now()->toDateString(),
        ];

        $firstId    = $ids[0]      ?? null;
        $firstStock = $stockIds[0] ?? null; // only filled for inventory

        if ($firstId !== null) {
            switch ($request->category) {
                case 'Order':
                    $payload['order_id'] = $firstId;
                    break;

                case 'Deliveries':
                    $payload['delivery_id'] = $firstId;
                    break;

                case 'Job Order':
                    $payload['joborder_id'] = $firstId;
                    break;

                case 'Inventory':
                    // store both inventory id and related stock id
                    $payload['stock_id'] = $firstStock ?? $firstId;
                    break;

                case 'Stock Out':
                case 'Stock Adjustment':
                    $payload['stock_id'] = $firstId;
                    break;

                case 'Payment':
                    $payload['payment_id'] = $firstId;
                    break;
            }
        }

        $report = Report::create($payload);

        return response()->json([
            'success'          => true,
            'report'           => $report,
            'orders'           => $ordersData,
            'order_ids'        => $ids,
            'stock_ids'        => $stockIds, // useful on front‑end if needed
            'generated_by_name'=> $generatedByName,
        ]);
    }

    private function buildOrderData(Request $request): array
    {
        $orders = Order::with(['customer', 'category'])
            ->whereBetween('order_date', [$request->date_from, $request->date_to])
            ->orderBy('order_date', 'desc')
            ->get();

        $ids        = $orders->pluck('order_id')->toArray();
        $ordersData = [];

        foreach ($orders as $order) {
            $ordersData[] = [
                'order_id'      => $order->order_id,
                'customer_name' => trim(($order->customer->fname ?? '') . ' ' . ($order->customer->lname ?? ''))
                                   ?: ($order->customer_id ?? '—'),
                'category_name' => $order->category->category_name ?? $order->category_id ?? 'N/A',
                'product_type'  => $order->product_type ?? 'N/A',
                'total_amount'  => $order->total_amount ?? 0,
                'order_date'    => $order->order_date,
                'status'        => $order->status ?? 'Pending',
            ];
        }

        return [$ordersData, $ids];
    }

    private function buildDeliveriesData(Request $request): array
    {
        $deliveries = Delivery::with(['supplier', 'employee'])
            ->whereBetween('delivery_date_request', [$request->date_from, $request->date_to])
            ->orderBy('delivery_date_request', 'desc')
            ->get();

        $ids  = $deliveries->pluck('delivery_id')->toArray();
        $data = [];

        foreach ($deliveries as $delivery) {
            $data[] = [
                'order_id'      => $delivery->delivery_id,
                'customer_name' => $delivery->supplier->supplier_name ?? '—',
                'category_name' => 'Delivery',
                'product_type'  => 'N/A',
                'total_amount'  => 0,
                'order_date'    => $delivery->delivery_date_request,
                'status'        => $delivery->status ?? 'Pending',
            ];
        }

        return [$data, $ids];
    }

    private function buildJobOrderData(Request $request): array
    {
        $jobOrders = Joborder::with(['employee', 'product'])
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->orderBy('created_at', 'desc')
            ->get();

        $ids  = $jobOrders->pluck('joborder_id')->toArray();
        $data = [];

        foreach ($jobOrders as $job) {
            $data[] = [
                'order_id'      => $job->joborder_id,
                'customer_name' => trim(($job->employee->fname ?? '') . ' ' . ($job->employee->lname ?? '')),
                'category_name' => 'Job Order',
                'product_type'  => $job->product->product_name ?? 'N/A',
                'total_amount'  => 0,
                'order_date'    => $job->created_at,
                'status'        => $job->status ?? 'Pending',
            ];
        }

        return [$data, $ids];
    }
   
    private function buildInventoryData(Request $request): array
    {
        $inventories = Inventory::with(['product', 'supplier'])
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->orderBy('created_at', 'desc')
            ->get();

        $ids  = $inventories->pluck('stock_id')->toArray();
        $data = [];

        foreach ($inventories as $inv) {
            $data[] = [
                'order_id'      => $inv->inventory_id,
                'stock_id'      => $inv->stock_id, // here you store the related stock id
                'customer_name' => $inv->supplier->supplier_name ?? '—',
                'category_name' => 'Inventory',
                'product_type'  => $inv->product->product_name ?? 'N/A',
                'total_amount'  => $inv->current_stock ?? 0,
                'order_date'    => $inv->created_at,
                'status'        => 'In Stock',
            ];
        }

        return [$data, $ids, $inventories->pluck('stock_id')->toArray()];
    }

    private function buildStockOutData(Request $request): array
    {
        $stockouts = StockOut::with(['employee', 'stock.product'])
            ->whereBetween('date_out', [$request->date_from, $request->date_to])
            ->orderBy('date_out', 'desc')
            ->get();

        $ids  = $stockouts->pluck('stockout_id')->toArray();
        $data = [];

        foreach ($stockouts as $out) {
            $data[] = [
                'order_id'      => $out->stockout_id,
                'customer_name' => trim(($out->employee->fname ?? '') . ' ' . ($out->employee->lname ?? '')),
                'category_name' => 'Stock Out',
                'product_type'  => $out->stock->product->product_name ?? 'N/A',
                'total_amount'  => $out->quantity_out ?? 0,
                'order_date'    => $out->date_out,
                'status'        => $out->status ?? 'Completed',
            ];
        }

        return [$data, $ids];
    }

    private function buildStockAdjustmentData(Request $request): array
    {
        $adjustments = StockAdjustment::with(['employee'])
            ->whereBetween('request_date', [$request->date_from, $request->date_to])
            ->orderBy('request_date', 'desc')
            ->get();

        $ids  = $adjustments->pluck('stockadjustment_id')->toArray();
        $data = [];

        foreach ($adjustments as $adj) {
            $data[] = [
                'order_id'      => $adj->stockadjustment_id,
                'customer_name' => trim(($adj->employee->fname ?? '') . ' ' . ($adj->employee->lname ?? '')),
                'category_name' => 'Stock Adjustment',
                'product_type'  => $adj->adjustment_type ?? 'N/A',
                'total_amount'  => $adj->quantity_adjusted ?? 0,
                'order_date'    => $adj->request_date,
                'status'        => $adj->status ?? 'Pending',
            ];
        }

        return [$data, $ids];
    }

    private function buildPaymentData(Request $request): array
    {
        $payments = Payment::with(['order.customer', 'employee'])
            ->whereBetween('payment_date', [$request->date_from, $request->date_to])
            ->orderBy('payment_date', 'desc')
            ->get();

        $ids  = $payments->pluck('payment_id')->toArray();
        $data = [];

        foreach ($payments as $payment) {
            $customerName = '—';
            if ($payment->order?->customer) {
                $customerName = trim(
                    ($payment->order->customer->fname ?? '') .
                    ' ' .
                    ($payment->order->customer->lname ?? '')
                ) ?: '—';
            }

            $data[] = [
                'order_id'      => $payment->payment_id,
                'customer_name' => $customerName,
                'category_name' => 'Payment',
                'product_type'  => $payment->payment_method ?? 'N/A',
                'total_amount'  => $payment->amount ?? 0,
                'order_date'    => $payment->payment_date,
                'status'        => $payment->status ?? 'Completed',
            ];
        }

        return [$data, $ids];
    }
}
