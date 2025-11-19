@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{
    showAddOrder: false,
    showOrderDetails: false,
    selectedOrderId: null
}">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Sales Transaction</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage purchase orders and their details for each transaction.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4">

    <!-- Search Input with Icon -->
    <div class="relative w-full md:w-1/4">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>

        <input type="text" placeholder="Search orders"
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm
                      focus:ring-2 focus:ring-black focus:outline-none" />
    </div>

    <!-- Buttons (right) -->
    <div class="flex gap-2">
    
        <!-- Include Order History Modal -->
        @include('added.order_history')

        <!-- Include Payment History Modal -->
        @include('added.payment_history')

        <!-- Add New Order Button -->
        <button type="button"
                @click="showAddOrder = true"
                class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2
                       hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Order</span>
        </button>

        <!-- Include Add Order Modal -->
        @include('added.add_order')
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
    <table id="order-table" class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order Date</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Total Amount</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
            </tr>
        </thead>
   <tbody class="divide-y divide-gray-100 relative">
    @foreach ($orders as $order)
        <tr class="group relative hover:bg-sky-200 cursor-pointer">
            <!-- Normal row content -->
            <td class="px-4 py-3 text-center font-medium text-gray-800 group-hover:opacity-0">
                O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
            </td>
            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
            </td>
            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                {{ $order->order_date }}
            </td>
            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                ₱{{ number_format($order->total_amount, 2) }}
            </td>
            <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                @php
                    $dotColor = match($order->status) {
                        'Pending' => 'bg-gray-500',
                        'Released' => 'bg-yellow-500',
                        'Approved' => 'bg-green-500',
                        'Cancelled' => 'bg-red-500',
                        default => 'bg-gray-400'
                    };
                @endphp

                <!-- Colored dot -->
                <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>

                <!-- Status text -->
                <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
            </td>
            <!-- Hover overlay for whole row -->
            <td colspan="5" class="absolute inset-0 flex items-center justify-center opacity-0 
                group-hover:opacity-100 transition-opacity duration-200 bg-sky-100">

                @if($order->status === 'Pending')
                    <!-- Only Details button -->
                    <button type="button"
                            class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                            @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                            Details
                        </span>
                    </button>

                @elseif($order->status === 'Released')
                    <!-- Details + Proceed Payment buttons -->
                    <div class="w-full h-full flex">
                        <!-- Details button -->
                        <button type="button"
                                class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                            <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                Details
                            </span>
                        </button>

                        <!-- Proceed Payment button -->
                        <button type="button"
                                @click="proceedPayment({{ $order->order_id }})"
                                class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors">
                            <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                Proceed Payment
                            </span>
                        </button>
                    </div>

                @else
                    <!-- Single Details button for other statuses -->
                    <button type="button"
                            class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                            @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                            Details
                        </span>
                    </button>
                @endif
            </td>
        </tr>
    @endforeach

    @if($orders->isEmpty())
        <tr>
            <td colspan="5" class="text-center py-4 text-gray-500">No orders found</td>
        </tr>
    @endif
</tbody>

    </table>
</div>


<!-- Order Details Modal -->
<div x-show="showOrderDetails" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">

    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative">

        <!-- Modal Header -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            Order Details - ID: <span x-text="selectedOrderId"></span>
        </h2>

        <!-- Order Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)

                        @php
                            $grandTotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                        @endphp

                        @foreach ($order->items ?? [] as $item)
                            <tr x-show="selectedOrderId === {{ $order->order_id }}">
                                <td class="px-4 py-2 text-center">
                                    OD{{ str_pad($item->orderdetails_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $item->stock->product->product_name ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $item->stock->product->unit ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    ₱{{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-4 py-2 text-right font-semibold">
                                    ₱{{ number_format($item->quantity * $item->price, 2) }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- GRAND TOTAL -->
                        <tr x-show="selectedOrderId === {{ $order->order_id }}">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">
                                GRAND TOTAL:
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">
                                ₱{{ number_format($grandTotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Close Button -->
        <div class="mt-6 flex justify-end">
            <button @click="showOrderDetails = false"
                    class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                Close
            </button>
        </div>

    </div>

</div>


<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600">
    <div id="order-pagination-info"></div>
    <ul id="order-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<script>
function openOrderModal(orderId) {
    const alpineRoot = document.querySelector('[x-data]');
    if (alpineRoot && Alpine) {
        Alpine.store('modal') || Alpine.store('modal', {});
        alpineRoot.__x.$data.selectedOrderId = orderId;
        alpineRoot.__x.$data.showOrderDetails = true;
    }
}

// Pagination JS
const orderRowsPerPage = 5;
const orderTableBody = document.getElementById('order-table-body');
const orderRows = Array.from(orderTableBody.querySelectorAll('tr'));
const orderPaginationLinks = document.getElementById('order-pagination-links');
const orderPaginationInfo = document.getElementById('order-pagination-info');
let orderCurrentPage = 1;
const orderTotalPages = Math.ceil(orderRows.length / orderRowsPerPage);

function showOrderPage(page) {
    orderCurrentPage = page;
    orderRows.forEach(row => row.style.display = 'none');
    const start = (page - 1) * orderRowsPerPage;
    const end = start + orderRowsPerPage;
    orderRows.slice(start, end).forEach(row => row.style.display = '');
    renderOrderPagination();
    const startItem = orderRows.length ? start + 1 : 0;
    const endItem = end > orderRows.length ? orderRows.length : end;
    orderPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${orderRows.length} results`;
}

function renderOrderPagination() {
    orderPaginationLinks.innerHTML = '';
    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = orderCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (orderCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => {
        e.preventDefault(); showOrderPage(orderCurrentPage - 1);
    });
    orderPaginationLinks.appendChild(prev);

    for (let i = 1; i <= orderTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === orderCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === orderCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== orderCurrentPage) li.querySelector('a').addEventListener('click', e => {
            e.preventDefault(); showOrderPage(i);
        });
        orderPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = orderCurrentPage === orderTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (orderCurrentPage !== orderTotalPages) next.querySelector('a').addEventListener('click', e => {
        e.preventDefault(); showOrderPage(orderCurrentPage + 1);
    });
    orderPaginationLinks.appendChild(next);
}

showOrderPage(1);
</script>
@endsection
