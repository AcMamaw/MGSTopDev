@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div x-data="{ showOrderDetails: false, selectedOrderId: null }">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Purchase Orders</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage purchase orders and their details for each transaction.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6">
    <div class="flex flex-col md:flex-row items-stretch justify-between gap-4">
        <!-- Search -->
        <div class="relative w-full md:w-1/4">
            <input type="text" id="order-search" placeholder="Search orders"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Add Order -->
        <a href="#"
            class="w-full md:w-auto bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-yellow-500 transition shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-plus">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Order</span>
        </a>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto">
    <div class="overflow-x-auto">
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
            <tbody id="order-table-body" class="divide-y divide-gray-100">
                @foreach ($orders as $order)
                    <tr class="group relative hover:bg-gray-50 cursor-pointer">
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
                       <td class="px-4 py-3 text-center group-hover:opacity-0">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'Pending')
                                    bg-gray-100 text-gray-700
                                @elseif($order->status === 'Released')
                                    bg-yellow-100 text-yellow-700
                                @elseif($order->status === 'Approved')
                                    bg-green-100 text-green-700
                                @elseif($order->status === 'Cancelled')
                                    bg-red-100 text-red-700
                                @else
                                    bg-blue-100 text-blue-700
                                @endif
                            ">
                                {{ $order->status }}
                            </span>
                        </td>

                        <!-- Hover overlay -->
                        <td colspan="5" class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition-opacity duration-200 
                            {{ $order->status == 'Approved' ? 'bg-green-400' : 'bg-yellow-100' }}">
                            
                            @if($order->status == 'Approved')
                                <a href="#" class="w-full h-full flex items-center justify-center">
                                    <span class="text-green-800 font-semibold text-sm hover:font-bold transition-all duration-200">
                                        Proceed Payment
                                    </span>
                                </a>
                            @else
                                <button type="button"
                                    class="w-full h-full flex items-center justify-center"
                                    @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                                    <span class="text-yellow-800 font-semibold text-sm hover:font-bold transition-all duration-200">
                                        Details
                                    </span>
                                </button>   
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Order Details Modal -->
<div x-show="showOrderDetails" x-transition
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Order Details:</h2>

        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($orders as $order)
                    @foreach ($order->details as $detail)
                        <tr x-show="selectedOrderId === {{ $order->order_id }}">
                            <td class="px-4 py-2 text-center">OD{{ str_pad($detail->orderdetails_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2 text-center">O{{ str_pad($detail->order_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2 text-center">{{ $detail->stock->product->product_name ?? '' }}</td>
                            <td class="px-4 py-2 text-right">{{ $detail->quantity }}</td>
                            <td class="px-4 py-2 text-right">₱{{ number_format($detail->price, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

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
