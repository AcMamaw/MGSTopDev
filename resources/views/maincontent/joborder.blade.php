@extends('layouts.app')

@section('title', 'My Job Orders')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="jobOrderComponent()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Job Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Assigned to: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Total Orders: <span class="font-bold text-gray-900">{{ $orders->count() }}</span></p>
        </div>
    </div>
    <p class="text-gray-600 mt-2">View and manage orders assigned to you.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex items-center justify-between gap-4">
    
    <!-- Left: Search and Filter -->
    <div class="flex items-center gap-3">
        <!-- Search Bar -->
        <div class="relative">
            <input type="text" 
                   x-model="searchQuery"
                   @input="filteredOrders"
                   placeholder="Search by Order ID, Customer..."
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none w-full md:w-80"
                   style="min-width:250px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2 whitespace-nowrap">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select x-model="statusFilter" @change="filteredOrders"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-yellow-400">
                <option value="all">All Status</option>
                <option value="Released">Released</option>
                <option value="In Progress">In Progress</option>
                <option value="Pending">Pending</option>
            </select>
        </div>
    </div>

    <!-- Right: History Button -->
    <div class="flex items-center gap-2">
        @include('added.joborder_history')
    </div>

</div>


<!-- Job Orders Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
    <table id="job-orders-table" class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Category</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order Date</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Total Amount</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Picked</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 relative">
            @forelse($orders as $order)
            <tr class="group relative hover:bg-white-200 cursor-pointer job-order-row"
                data-status="{{ $order->status }}"
                data-search="O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }} {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }} {{ $order->status }}">
                <!-- Order ID -->
                <td class="px-4 py-3 text-center font-medium text-gray-800 group-hover:opacity-0">
                    O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                </td>

                <!-- Customer Name -->
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                </td>

                <!-- Category -->
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $order->category->category_name ?? 'N/A' }}
                </td>

                <!-- Product Type -->
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ $order->product_type ?? 'N/A' }}
                </td>

                <!-- Order Date -->
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                </td>

                <!-- Total Amount -->
                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                    ₱{{ number_format($order->total_amount, 2) }}
                </td>

                <!-- Status -->
                <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                    @php
                        $dotColor = match($order->status) {
                            'Pending' => 'bg-gray-500',
                            'In Progress' => 'bg-yellow-500',
                            'Released' => 'bg-blue-500',
                            'Completed' => 'bg-green-500',
                            default => 'bg-gray-400'
                        };
                    @endphp
                    <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                    <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                </td>

                <!-- Picked Status -->
                <td class="px-4 py-3 text-center group-hover:opacity-0">
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

                <!-- Hover overlay for whole row -->
                <td colspan="8" class="absolute inset-0 flex items-center justify-center opacity-0 
                    group-hover:opacity-100 transition-opacity duration-200 bg-white-100">

                    <div class="w-full h-full flex">
                        <!-- View Details Button -->
                        <button type="button"
                            class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                            @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                            <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">View Details</span>
                        </button>

                        <!-- Action Button - PRIORITY: Always show Pick Job Order first if not picked -->
                        @if(!$order->is_picked)
                            <!-- Show Pick Job Order if not picked yet (regardless of status) -->
                            <button type="button"
                                class="flex-1 flex items-center justify-center bg-yellow-200 hover:bg-yellow-300 transition-colors"
                                @click.stop="pickJobOrder({{ $order->order_id }})">
                                <span class="text-yellow-700 font-semibold text-sm hover:font-bold transition-all duration-200">Pick Job Order</span>
                            </button>
                        @elseif($order->is_picked && $order->status !== 'Released')
                            <!-- Show Done Job Order if picked and not yet released -->
                            <button type="button"
                                class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors"
                                @click.stop="doneJobOrder({{ $order->order_id }})">
                                <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">Done Job Order</span>
                            </button>
                        @elseif($order->status === 'Released')
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-state">
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg font-medium">No orders assigned to you</p>
                    <p class="text-sm mt-1">Check back later for new assignments</p>
                </td>
            </tr>
            @endforelse
            
            <!-- Dynamic "No Results" message when filter returns nothing -->
            @if($orders->isNotEmpty())
            <tr class="empty-state" style="display: none;">
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No orders match your filter</p>
                    <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="joborder-pagination-info"></div>
    <ul id="joborder-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

<!-- Order Details Modal -->
<div x-show="showOrderDetails" x-cloak x-transition
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            Order Details - <span x-text="'O' + String(selectedOrderId).padStart(3, '0')"></span>
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Color</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        @foreach ($order->items ?? [] as $item)
                        <tr x-show="selectedOrderId === {{ $order->order_id }}">
                            <td class="px-4 py-2 text-center">OD{{ str_pad($item->orderdetails_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->stock->product->product_name ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->size ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <span class="w-4 h-4 rounded-full border" 
                                          style="background-color: {{ $item->color ?? '#ffffff' }};"></span>
                                    <span>{{ $item->color ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right">₱{{ number_format($item->price, 2) }}</td>
                            <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity * $item->price, 2) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="showOrderDetails = false"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Job Order History Modal -->
<div x-show="showJobOrderHistory" x-cloak x-transition
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Job Order History</h2>
            <button @click="showJobOrderHistory = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- History Search -->
        <div class="mb-4">
            <div class="relative">
                <input type="text" 
                       x-model="historySearchQuery"
                       @input="filterHistory()"
                       placeholder="Search orders..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>
        </div>

        <div x-show="historyLoading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600"></div>
            <p class="mt-2 text-gray-600">Loading history...</p>
        </div>

        <div x-show="!historyLoading" class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Product Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Issued By</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Order Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Completed Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="order in filteredHistory" :key="order.order_id">
                        <tr class="group relative hover:bg-green-100 cursor-pointer">
                            <td class="px-4 py-3 text-center font-medium text-gray-800 group-hover:opacity-0" x-text="'O' + String(order.order_id).padStart(3, '0')"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="(order.customer?.fname || '') + ' ' + (order.customer?.lname || '')"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="order.category?.category_name || 'N/A'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="order.product_type || 'N/A'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="order.employee ? (order.employee.fname + ' ' + order.employee.lname) : '-'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="new Date(order.order_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="order.updated_at ? new Date(order.updated_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : '-'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0" x-text="'₱' + parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                            <td class="px-4 py-3 text-center group-hover:opacity-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                    <span x-text="order.status"></span>
                                </span>
                            </td>
                            
                            <!-- Details Button on Hover -->
                            <td colspan="9" class="absolute inset-0 flex items-center justify-center opacity-0 
                                group-hover:opacity-100 transition-opacity duration-200 bg-green-100">
                                <button type="button"
                                    class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                    @click="viewHistoryOrderDetails(order.order_id)">
                                    <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                        Details
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredHistory.length === 0">
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No completed orders found</p>
                            <p class="text-sm mt-1">Your completed job orders will appear here</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="showJobOrderHistory = false"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- History Order Details Modal -->
<div x-show="showHistoryOrderDetails" x-cloak x-transition
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Order Details - ID: <span x-text="'O' + String(selectedHistoryOrderId).padStart(3, '0')"></span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Color</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in historyOrderDetails" :key="item.orderdetails_id">
                        <tr>
                            <td class="px-4 py-2 text-center" x-text="'OD' + String(item.orderdetails_id).padStart(3, '0')"></td>
                            <td class="px-4 py-2 text-center" x-text="item.stock?.product?.product_name || '-'"></td>
                            <td class="px-4 py-2 text-center" x-text="item.size || '-'"></td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <span class="w-4 h-4 rounded-full" :style="'background-color: ' + (item.color || '#ffffff')"></span>
                                    <span x-text="item.color || '-'"></span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center" x-text="item.quantity"></td>
                            <td class="px-4 py-2 text-center" x-text="item.stock?.product?.unit || '-'"></td>
                            <td class="px-4 py-2 text-right" x-text="'₱' + parseFloat(item.price).toFixed(2)"></td>
                            <td class="px-4 py-2 text-right font-semibold" x-text="'₱' + (item.quantity * item.price).toFixed(2)"></td>
                        </tr>
                    </template>
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="'₱' + historyOrderGrandTotal.toFixed(2)"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="showHistoryOrderDetails = false; showJobOrderHistory = true"
                    class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

</div>

<script>
function jobOrderComponent() {
    return {
        searchQuery: '',
        statusFilter: 'all',
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

        // Filter orders based on status
        filteredOrders() {
            const rows = document.querySelectorAll('#job-orders-table tbody tr.job-order-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const searchText = row.getAttribute('data-search').toLowerCase();
                const query = this.searchQuery.toLowerCase();
                
                const matchesStatus = this.statusFilter === 'all' || status === this.statusFilter;
                const matchesSearch = !query || searchText.includes(query);
                
                if (matchesStatus && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide empty state
            const emptyStates = document.querySelectorAll('#job-orders-table .empty-state');
            emptyStates.forEach(state => {
                state.style.display = visibleCount === 0 ? '' : 'none';
            });
            
            // Reset pagination after filtering
            if (window.showJobOrderPage) {
                window.showJobOrderPage(1);
            }
        },

        // Open Job Order History Modal
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
                console.log('Loaded history:', this.historyOrders);
            } catch (error) {
                console.error('Error loading history:', error);
                alert('Failed to load job order history');
            } finally {
                this.historyLoading = false;
            }
        },

        // Filter history
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
                
                return orderId.toLowerCase().includes(query) || 
                       customerName.includes(query) || 
                       category.includes(query) || 
                       productType.includes(query);
            });
        },

        // View History Order Details
        async viewHistoryOrderDetails(orderId) {
            this.selectedHistoryOrderId = orderId;
            this.showJobOrderHistory = false;
            this.showHistoryOrderDetails = true;

            // Find order details from loaded history
            const order = this.historyOrders.find(o => o.order_id === orderId);
            if (order && order.items) {
                this.historyOrderDetails = order.items;
                this.historyOrderGrandTotal = order.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            }
        },

        // Pick Job Order
        pickJobOrder(orderId) {
            if (!confirm('Pick this job order? This will:\n- Create stockout entries for all items\n- Reduce inventory stock\n- Change status to "In Progress"')) {
                return;
            }

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

        // Done Job Order
        doneJobOrder(orderId) {
            if (!confirm('Mark this job order as DONE? This will change status to "Released" (Ready for Pickup).')) {
                return;
            }

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
    }
}

// ==================== PAGINATION - Initialize after DOM loads ====================
document.addEventListener('DOMContentLoaded', function() {
    const jobOrderRowsPerPage = 5;
    const jobOrderTableBody = document.querySelector('#job-orders-table tbody');
    
    if (!jobOrderTableBody) {
        console.error('❌ Table body not found!');
        return;
    }
    
    const allJobOrderRows = Array.from(jobOrderTableBody.querySelectorAll('tr.job-order-row'));
    const jobOrderPaginationLinks = document.getElementById('joborder-pagination-links');
    const jobOrderPaginationInfo = document.getElementById('joborder-pagination-info');

    console.log('✅ Found', allJobOrderRows.length, 'job order rows');

    let jobOrderCurrentPage = 1;

    window.getVisibleJobOrderRows = function() {
        return allJobOrderRows.filter(row => row.style.display !== 'none');
    }

    window.showJobOrderPage = function(page) {
        const visibleRows = window.getVisibleJobOrderRows();
        const jobOrderTotalPages = Math.ceil(visibleRows.length / jobOrderRowsPerPage) || 1;
        
        console.log('📄 Showing page', page, 'with', visibleRows.length, 'visible rows');
        
        jobOrderCurrentPage = page;
        allJobOrderRows.forEach(row => row.style.display = 'none');
        
        const start = (page - 1) * jobOrderRowsPerPage;
        const end = start + jobOrderRowsPerPage;
        visibleRows.slice(start, end).forEach(row => row.style.display = '');
        
        window.renderJobOrderPagination(jobOrderTotalPages, visibleRows.length);
    }

    window.renderJobOrderPagination = function(totalPages, totalResults) {
        jobOrderPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = jobOrderCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
        if (jobOrderCurrentPage !== 1) prev.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showJobOrderPage(jobOrderCurrentPage - 1); });
        jobOrderPaginationLinks.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === jobOrderCurrentPage ? ' bg-sky-400 text-white' : '');
            li.innerHTML = i === jobOrderCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== jobOrderCurrentPage) li.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showJobOrderPage(i); });
            jobOrderPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = jobOrderCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
        if (jobOrderCurrentPage !== totalPages) next.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showJobOrderPage(jobOrderCurrentPage + 1); });
        jobOrderPaginationLinks.appendChild(next);
        
        const start = (jobOrderCurrentPage - 1) * jobOrderRowsPerPage + 1;
        const end = Math.min(jobOrderCurrentPage * jobOrderRowsPerPage, totalResults);
        jobOrderPaginationInfo.textContent = `Showing ${totalResults ? start : 0} to ${end} of ${totalResults} results`;
    }

    window.showJobOrderPage(1);
});
</script>

@endsection
