@extends('layouts.app')

@section('title', 'My Job Orders')

@section('content')
<style>
    [x-cloak]{display:none!important}
</style>

<div x-data="jobOrderComponent()" x-init="init()">

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <path d="M10 12h4"></path>
                    <path d="M10 16h4"></path>
                </svg>
                My Job Orders
            </h1>
            <div class="text-right">
                <p class="text-sm text-gray-600 mt-1">
                    Assigned to:
                    <span class="font-semibold text-gray-900">
                        {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
                    </span>
                </p>
                <p class="text-sm text-gray-600">
                    Total Orders:
                    <span class="font-bold text-yellow-500">{{ $orders->count() }}</span>
                </p>
            </div>
        </div>
        <p class="text-gray-600 mt-2 text-md">
            View and manage orders that have been assigned to you for processing.
        </p>
    </header>

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 max-w-full mx-auto">
        {{-- Left: search + filter --}}
        <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full sm:w-80">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="applyFilters()"
                    placeholder="Search orders"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none transition"
                >
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            {{-- Status filter --}}
            <div class="flex items-center gap-2">
                <label for="status-filter" class="text-sm font-semibold text-gray-700 hidden sm:block">
                    Status:
                </label>
                <select
                    id="status-filter"
                    x-model="statusFilter"
                    @change="applyFilters()"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer"
                >
                    <option value="all">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Released">Released</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>

        {{-- Right: History button --}}
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            @include('added.order_history2')
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="job-orders-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Order ID</th>
                        <th class="px-4 py-3 text-left  text-xs font-bold uppercase text-gray-600 tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Product Type</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Order Date</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Total Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Picked</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 relative">
                    @forelse($orders as $order)
                        <tr
                            class="job-order-row cursor-pointer transition-colors duration-200"
                            data-status="{{ $order->status }}"
                            data-category="{{ $order->category->category_name ?? 'N/A' }}"
                            data-product-type="{{ $order->product_type ?? 'N/A' }}"
                            data-search="O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                {{ $order->customer->fname ?? '' }}
                                {{ $order->customer->lname ?? '' }}
                                {{ $order->category->category_name ?? '' }}
                                {{ $order->product_type ?? '' }}
                                {{ $order->status }}"
                        >
                            <td class="px-4 py-3 text-center font-medium text-gray-800">
                                O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-4 py-3 text-left text-gray-600">
                                {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $order->category->category_name ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $order->product_type ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center flex justify-center items-center space-x-2">
                                @php
                                    $dotColor = match($order->status) {
                                        'Pending'     => 'bg-gray-500',
                                        'In Progress' => 'bg-yellow-500',
                                        'Released'    => 'bg-blue-500',
                                        'Completed'   => 'bg-green-500',
                                        default       => 'bg-gray-400'
                                    };
                                @endphp
                                <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                                <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if($order->is_picked)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        ✓ Picked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        Not Picked
                                    </span>
                                @endif
                            </td>

                            {{-- ACTION BUTTON: opens job-order actions/details modal --}}
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    @click="
                                        selectedOrderId = {{ $order->order_id }};
                                        showOrderDetails = true
                                    "
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 shadow-sm transition"
                                    title="View job order"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-base">
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No orders assigned to you</p>
                                    <p class="text-sm mt-1">Check back later for new assignments</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    @if($orders->isNotEmpty())
                        <tr class="empty-state-filtered" style="display:none;">
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No orders match your filter</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto pb-8">
        <div id="joborder-pagination-info"></div>
        <ul id="joborder-pagination-links" class="pagination-links flex gap-2"></ul>
    </div>

    <div x-show="showOrderDetails" x-transition x-cloak
        class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showOrderDetails = false"
            class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            {{-- HEADER WITH CLOSE --}}
            <div class="flex items-center justify-between mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-gray-800">
                    Order Details - ID:
                    <span class="text-black-600"
                        x-text="'O' + selectedOrderId.toString().padStart(3, '0')"></span>
                </h2>

                <button type="button"
                        @click="showOrderDetails = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>

            {{-- DETAILS TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Detail ID</th>
                            <th class="px-4 py-3 text-left  text-xs font-bold uppercase text-gray-600">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Size</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Color</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Quantity</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Unit</th>
                            <th class="px-4 py-3 text-right  text-xs font-bold uppercase text-gray-600">Price</th>
                            <th class="px-4 py-3 text-right  text-xs font-bold uppercase text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                            @php
                                $grandTotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                                $orderStatus = $order->status;
                                $isPicked    = $order->is_picked;
                            @endphp

                            @foreach ($order->items ?? [] as $item)
                                <tr x-show="selectedOrderId === {{ $order->order_id }}"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-center text-sm font-semibold text-yellow-700">
                                        OD{{ str_pad($item->orderdetails_id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4 py-2 text-left text-sm text-gray-800">
                                        {{ $item->stock->product->product_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700">
                                        {{ $item->size ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700">
                                        <div class="flex items-center justify-center space-x-1">
                                            <span class="w-4 h-4 rounded-full border border-gray-300"
                                                style="background-color: {{ $item->color ?? '#ffffff' }};"></span>
                                            <span>{{ $item->color ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700 font-medium">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700">
                                        {{ $item->stock->product->unit ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-700">
                                        ₱{{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-semibold text-gray-900">
                                        ₱{{ number_format($item->quantity * $item->price, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr x-show="selectedOrderId === {{ $order->order_id }}" class="bg-gray-100">
                                <td colspan="7" class="px-4 py-3 text-right font-bold text-base text-gray-700">
                                    GRAND TOTAL:
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-base text-gray-900">
                                    ₱{{ number_format($grandTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ACTIONS: PICK / DONE JOB ORDER --}}
            <div class="mt-6 w-full">
                <div class="mb-2 flex justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">
                        Action
                    </span>
                </div>

                <div class="flex justify-center">
                    @foreach ($orders as $order)
                        <template x-if="selectedOrderId === {{ $order->order_id }}">
                            @php
                                $orderStatus = $order->status;
                                $isPicked    = $order->is_picked;
                                // Pick allowed when not picked yet
                                $canPick     = !$isPicked;
                                // Done allowed when picked and status In Progress
                                $canDone     = $isPicked && $orderStatus === 'In Progress';
                            @endphp

                            <div class="flex flex-col items-center gap-3">
                                <div class="flex flex-wrap justify-center gap-3">
                                    {{-- Pick Job Order: when not picked --}}
                                    <button
                                        type="button"
                                        @if ($canPick)
                                            @click="pickJobOrder({{ $order->order_id }})"
                                        @endif
                                        class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                            {{ $canPick
                                                ? 'bg-yellow-400 text-black hover:bg-yellow-500 cursor-pointer'
                                                : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                        @if (!$canPick)
                                            disabled
                                        @endif
                                    >
                                        Pick Job Order
                                    </button>

                                    {{-- Done Job Order: when picked and status In Progress --}}
                                    <button
                                        type="button"
                                        @if ($canDone)
                                            @click="doneJobOrder({{ $order->order_id }})"
                                        @endif
                                        class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                            {{ $canDone
                                                ? 'bg-green-500 text-black hover:bg-green-600 cursor-pointer'
                                                : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                        @if (!$canDone)
                                            disabled
                                        @endif
                                    >
                                        Done Job Order
                                    </button>
                                </div>

                                <p class="mt-1 text-[11px] text-gray-400">
                                    Status: {{ $orderStatus }} &mdash; Picked:
                                    {{ $isPicked ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </template>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function jobOrderComponent() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        categoryFilter: 'all',
        productTypeFilter: 'all',

        showOrderDetails: false,
        selectedOrderId: null,

        showJobOrderHistory: false,
        historyLoading: false,
        historyOrders: [],
        historySearchQuery: '',
        filteredHistory: [],
        showHistoryOrderDetails: false,
        selectedHistoryOrderId: null,
        historyOrderDetails: [],
        historyOrderGrandTotal: 0,

        init() {
            if (window.showJobOrderPage) {
                window.showJobOrderPage(1);
            }
        },

        applyFilters() {
            const rows = document.querySelectorAll('#job-orders-table tbody tr.job-order-row');
            let visibleCount = 0;

            const query = this.searchQuery.toLowerCase().trim();
            const statusFilter = this.statusFilter;
            const categoryFilter = this.categoryFilter;
            const productTypeFilter = this.productTypeFilter;

            rows.forEach(row => {
                const rowStatus      = row.getAttribute('data-status') || '';
                const rowCategory    = row.getAttribute('data-category') || '';
                const rowProductType = row.getAttribute('data-product-type') || '';
                const searchText     = (row.getAttribute('data-search') || '').toLowerCase();

                const matchesStatus =
                    statusFilter === 'all' || rowStatus === statusFilter;

                const matchesCategory =
                    categoryFilter === 'all' || rowCategory === categoryFilter;

                const matchesProductType =
                    productTypeFilter === 'all' || rowProductType === productTypeFilter;

                const matchesSearch =
                    !query || searchText.includes(query);

                if (matchesStatus && matchesCategory && matchesProductType && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const baseEmpty = document.querySelector('#job-orders-table .empty-state-base');
            const filteredEmpty = document.querySelector('#job-orders-table .empty-state-filtered');
            const anyRows = document.querySelectorAll('#job-orders-table tr.job-order-row').length > 0;

            if (!anyRows) {
                if (baseEmpty) baseEmpty.style.display = '';
                if (filteredEmpty) filteredEmpty.style.display = 'none';
            } else {
                if (baseEmpty) baseEmpty.style.display = 'none';
                if (filteredEmpty) filteredEmpty.style.display = visibleCount === 0 ? '' : 'none';
            }

            if (window.showJobOrderPage) {
                window.showJobOrderPage(1);
            }
        },

        async openJobOrderHistory() {
            this.showJobOrderHistory = true;
            this.historyLoading = true;
            this.historyOrders = [];
            this.historySearchQuery = '';

            try {
                const response = await fetch('/joborders/history', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load history');
                }

                const data = await response.json();
                this.historyOrders = data.orders || [];
                this.filteredHistory = this.historyOrders;
            } catch (error) {
                console.error('Error loading history:', error);
                alert('Failed to load job order history');
            } finally {
                this.historyLoading = false;
            }
        },

        filterHistory() {
            const query = this.historySearchQuery.toLowerCase();

            if (!query) {
                this.filteredHistory = this.historyOrders;
                return;
            }

            this.filteredHistory = this.historyOrders.filter(order => {
                const orderId = 'O' + String(order.order_id).padStart(3, '0');
                const customerName = ((order.customer?.fname || '') + ' ' + (order.customer?.lname || '')).toLowerCase();
                const category = (order.category?.category_name || '').toLowerCase();
                const productType = (order.product_type || '').toLowerCase();
                const status = (order.status || '').toLowerCase();

                return (
                    orderId.toLowerCase().includes(query) ||
                    customerName.includes(query) ||
                    category.includes(query) ||
                    productType.includes(query) ||
                    status.includes(query)
                );
            });
        },

        async viewHistoryOrderDetails(orderId) {
            this.selectedHistoryOrderId = orderId;
            this.showJobOrderHistory = false;
            this.showHistoryOrderDetails = true;

            const order = this.historyOrders.find(o => o.order_id === orderId);
            if (order && order.items) {
                this.historyOrderDetails = order.items;
                this.historyOrderGrandTotal = order.items.reduce(
                    (sum, item) => sum + (item.quantity * item.price),
                    0
                );
            }
        },

        pickJobOrder(orderId) {
            if (!confirm('Pick this job order?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/joborders/${orderId}/pick`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to pick job order');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred: ' + err.message);
                });
        },

        doneJobOrder(orderId) {
            if (!confirm('Are you sure the Job Order already done?'))
                return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/joborders/${orderId}/done`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to mark job order as done');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred: ' + err.message);
                });
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const jobOrderRowsPerPage = 5;
    const jobOrderTableBody = document.querySelector('#job-orders-table tbody');
    if (!jobOrderTableBody) return;

    const allJobOrderRows = Array.from(jobOrderTableBody.querySelectorAll('tr.job-order-row'));
    const jobOrderPaginationLinks = document.getElementById('joborder-pagination-links');
    const jobOrderPaginationInfo = document.getElementById('joborder-pagination-info');

    let jobOrderCurrentPage = 1;

    window.getVisibleJobOrderRows = function () {
        return allJobOrderRows.filter(row => row.style.display !== 'none');
    };

    window.showJobOrderPage = function (page) {
        const visibleRows = window.getVisibleJobOrderRows();
        const jobOrderTotalPages = Math.ceil(visibleRows.length / jobOrderRowsPerPage) || 1;

        jobOrderCurrentPage = page;
        allJobOrderRows.forEach(row => (row.style.display = 'none'));

        const start = (page - 1) * jobOrderRowsPerPage;
        const end = start + jobOrderRowsPerPage;
        visibleRows.slice(start, end).forEach(row => (row.style.display = ''));

        renderJobOrderPagination(jobOrderTotalPages, visibleRows.length);
    };

    function renderJobOrderPagination(totalPages, totalResults) {
        jobOrderPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = jobOrderCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
        if (jobOrderCurrentPage !== 1) {
            prev.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                window.showJobOrderPage(jobOrderCurrentPage - 1);
            });
        }
        jobOrderPaginationLinks.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className =
                'border rounded px-2 py-1' +
                (i === jobOrderCurrentPage ? ' bg-yellow-400 text-black' : '');
            li.innerHTML = i === jobOrderCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== jobOrderCurrentPage) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    window.showJobOrderPage(i);
                });
            }
            jobOrderPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = jobOrderCurrentPage === totalPages ? 'Next »' : '<a href="#">Next »</a>';
        if (jobOrderCurrentPage !== totalPages) {
            next.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                window.showJobOrderPage(jobOrderCurrentPage + 1);
            });
        }
        jobOrderPaginationLinks.appendChild(next);

        const start = (jobOrderCurrentPage - 1) * jobOrderRowsPerPage + 1;
        const end = Math.min(jobOrderCurrentPage * jobOrderRowsPerPage, totalResults);
        jobOrderPaginationInfo.textContent = `Showing ${
            totalResults ? start : 0
        } to ${end} of ${totalResults} results`;
    }

    window.renderJobOrderPagination = renderJobOrderPagination;
    window.showJobOrderPage(1);
});
</script>
@endsection
