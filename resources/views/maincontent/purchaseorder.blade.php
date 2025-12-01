@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="paymentComponent()">

<header class="mb-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900">Sales Transaction</h1>
    </div>
    <p class="text-gray-600 mt-2">Manage purchase orders and their details for each transaction.</p>
</header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex items-center justify-between gap-4">

    <!-- Left: Search and Filter -->
    <div class="flex items-center gap-3">
        
        <!-- Search Input -->
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
            <input type="text"
                   x-model="searchQuery"
                   @input="filterOrders()"
                   placeholder="Search by Order ID"
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black focus:outline-none w-full md:w-80"
                   style="min-width:250px;" />
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2 whitespace-nowrap">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select x-model="statusFilter" @change="filterOrders()"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                <option value="all">All Status</option>
                <option value="Released">Released</option>
                <option value="In Progress">In Progress</option>
                <option value="Pending">Pending</option>
            </select>
        </div>

    </div>

    <!-- Right: History Buttons and Add New Order Button -->
    <div class="flex items-center gap-2">
        <!-- Include Order History Modal -->
        @include('added.order_history')

        <!-- Include Payment History Modal -->
        @include('added.payment_history')

        <!-- Add New Order Button -->
        <button type="button"
                @click="showAddOrder = true"
                class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center gap-2
                       hover:bg-yellow-500 transition shadow-md whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Order</span>
        </button>

        <!-- Include Add Order Modal -->
        @include('added.add_order')

        <!-- Include the Assign Job Order modal -->
        @include('added.assign_joborders')
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
    <table id="order-table" class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order ID</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Customer</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Category</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Order Date</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Total Amount</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Balance</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Payment</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 relative">
            @forelse ($orders as $order)
                @if ($order->status !== 'Completed')
                    <tr class="group relative hover:bg-sky-200 cursor-pointer order-row"
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

                        <!-- Category Name -->
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

                        <!-- Balance -->
                        <td class="px-4 py-3 text-center group-hover:opacity-0">
                            @php
                                $balance = $order->payment->balance ?? $order->total_amount;
                            @endphp
                            ₱{{ number_format($balance, 2) }}
                        </td>

                        <!-- Payment Status -->
                        <td class="px-4 py-3 text-center group-hover:opacity-0">
                            @php
                                $payStatus = $order->payment->status ?? 'Not Paid';
                            @endphp
                            {{ $payStatus }}
                        </td>

                        <!-- Order Status -->
                        <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                            @php
                                $dotColor = match($order->status) {
                                    'Pending' => 'bg-gray-500',
                                    'In Progress' => 'bg-yellow-500',
                                    'Released' => 'bg-blue-500',
                                    'Completed' => 'bg-green-500',
                                    'Cancelled' => 'bg-red-500',
                                    default => 'bg-gray-400'
                                };
                            @endphp
                            <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                            <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                        </td>

                         <!-- Hover overlay for whole row -->
                        <td colspan="9" class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition-opacity duration-200 bg-sky-100">

                            @php 
                                $payStatus = $order->payment->status ?? 'Not Paid';
                                $orderStatus = $order->status;
                                $isPartialPayment = ($payStatus === 'Partial');
                                $isFullyPaid = ($payStatus === 'Fully Paid');
                            @endphp

                            <div class="w-full h-full flex">

                                <!-- Details Button always -->
                                <button type="button"
                                    class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                    @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails = true">
                                    <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">Details</span>
                                </button>

                                @if ($orderStatus === 'Pending')
                                    <!-- Assign Job Order -->
                                  <button type="button"
                                        class="flex-1 flex items-center justify-center bg-purple-200 hover:bg-purple-300 transition-colors"
                                        @click.stop="openAssignJobOrder({{ $order->order_id }})">
                                        <span class="text-purple-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Assign Job Order
                                        </span>
                                    </button>

                                    <!-- Complete Payment (only if partial) -->
                                    @if ($isPartialPayment)
                                        <button type="button"
                                            class="flex-1 flex items-center justify-center bg-yellow-200 hover:bg-yellow-300 transition-colors"
                                            @click="openPaymentModal({{ $order->order_id }}, {{ $order->payment->balance ?? $order->total_amount }})">
                                            <span class="text-yellow-700 font-semibold text-sm hover:font-bold transition-all duration-200">Complete Payment</span>
                                        </button>
                                    @endif

                                @elseif ($orderStatus === 'In Progress' || $orderStatus === 'Released')
                                    <!-- Complete Payment (only if partial) -->
                                    @if ($isPartialPayment)
                                        <button type="button"
                                            class="flex-1 flex items-center justify-center bg-yellow-200 hover:bg-yellow-300 transition-colors"
                                            @click="openPaymentModal({{ $order->order_id }}, {{ $order->payment->balance ?? $order->total_amount }})">
                                            <span class="text-yellow-700 font-semibold text-sm hover:font-bold transition-all duration-200">Complete Payment</span>
                                        </button>
                                    @elseif ($isFullyPaid && $orderStatus === 'Released')
                                        <!-- Ready to Claim (only if fully paid and released) -->
                                        <button type="button"
                                            class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors"
                                            @click="markAsCompleted({{ $order->order_id }})">
                                            <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">Ready to Claim</span>
                                        </button>
                                    @endif
                                @endif
                        </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr class="empty-state-no-orders">
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-lg font-medium">No orders available</p>
                        <p class="text-sm mt-1">Create a new order to get started</p>
                    </td>
                </tr>
            @endforelse

            <!-- Dynamic empty state when filter returns no results -->
            @if($orders->isNotEmpty())
            <tr class="empty-state-filter" style="display: none;">
                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
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

@include('added.payment_complete')

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="order-pagination-info"></div>
    <ul id="order-pagination-links" class="pagination-links flex gap-2"></ul>
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
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Color</th>
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
                                    {{ $item->size ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <span class="w-4 h-4 rounded-full" 
                                            style="background-color: {{ $item->color ?? '#ffffff' }};">
                                        </span>
                                        <span>{{ $item->color ?? '-' }}</span>
                                    </div>
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
                            <td colspan="7" class="px-4 py-3 text-right font-bold text-gray-700">
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
</div>

<script>
function markAsCompleted(orderId) {
    fetch(`/orders/${orderId}/complete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}

function paymentComponent() {
    return {
        // Shared properties
        showAddOrder: false,
        showOrderDetails: false,
        searchQuery: '',
        statusFilter: 'all',

        // Assign Job Order modal
        showAssignJobOrderModal: false,
        employees: [],
        selectedEmployees: [],
        selectAllEmployees: false,
        employeeSearch: '',

        // Payment modal
        showCompletePaymentModal: false,
        paymentBalance: 0,
        paymentCash: 0,
        paymentMethod: '',
        paymentReference: '',

        // Selected Order
        selectedOrderId: null,

        // Filter orders
        filterOrders() {
            const rows = document.querySelectorAll('.order-row');
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
            const emptyStateFilter = document.querySelector('.empty-state-filter');
            if (emptyStateFilter) {
                emptyStateFilter.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
            }
            
            // Reset pagination after filtering
            if (window.showOrderPage) {
                window.showOrderPage(1);
            }
        },

        // --------------------------
        // Assign Job Order Methods
        // --------------------------
        async openAssignJobOrder(orderId) {
            console.log('🔵 Opening assign modal for order:', orderId);
            
            this.selectedOrderId = orderId;
            this.showAssignJobOrderModal = true;
            this.selectedEmployees = [];
            this.selectAllEmployees = false;
            this.employees = [];
            this.employeeSearch = '';

            try {
                console.log('🔵 Fetching employees from /employees/active');
                
                const res = await fetch('/employees/active', { 
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                console.log('🔵 Response status:', res.status);
                console.log('🔵 Response OK:', res.ok);
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('❌ Failed to load employees:', res.statusText, errorText);
                    alert('Failed to load employees: ' + res.statusText);
                    this.employees = [];
                    return;
                }
                
                const data = await res.json();
                console.log('🔵 Raw API response:', data);
                
                if (data.employees && Array.isArray(data.employees)) {
                    this.employees = data.employees;
                    console.log('✅ Loaded employees:', this.employees);
                    console.log('✅ Total employees:', this.employees.length);
                } else {
                    console.error('❌ Invalid data format:', data);
                    this.employees = [];
                    alert('Invalid employee data format received');
                }
                
            } catch (err) {
                console.error('❌ Error fetching employees:', err);
                alert('Error loading employees: ' + err.message);
                this.employees = [];
            }
        },

        toggleSelectAll() {
            if (this.selectAllEmployees) {
                const filteredEmployees = this.employees.filter(e => {
                    if (!this.employeeSearch) return true;
                    const search = this.employeeSearch.toLowerCase();
                    const fullName = (e.fname + ' ' + e.lname).toLowerCase();
                    return fullName.includes(search);
                });
                
                this.selectedEmployees = filteredEmployees.map(e => e.employee_id);
                console.log('✅ Selected all employees:', this.selectedEmployees);
            } else {
                this.selectedEmployees = [];
                console.log('✅ Deselected all employees');
            }
        },

        submitAssignJobOrder() {
            console.log('🔵 Submitting job order assignment');
            console.log('🔵 Order ID:', this.selectedOrderId);
            console.log('🔵 Selected employees:', this.selectedEmployees);
            
            if (this.selectedEmployees.length === 0) {
                alert('Please select at least one employee.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/orders/assign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: this.selectedOrderId,
                    employees: this.selectedEmployees
                })
            })
            .then(res => {
                console.log('🔵 Assign response status:', res.status);
                return res.json();
            })
            .then(data => {
                console.log('🔵 Assign response data:', data);
                
                if (data.success) {
                    alert('Job order assigned successfully!');
                    this.showAssignJobOrderModal = false;
                    this.selectedEmployees = [];
                    location.reload(); 
                } else {
                    alert(data.message || 'Failed to assign job order.');
                }
            })
            .catch(err => {
                console.error('❌ Assign job order error:', err);
                alert('An error occurred while assigning job order: ' + err.message);
            });
        },

        // --------------------------
        // Payment Methods
        // --------------------------
        openPaymentModal(orderId, balance) {
            this.selectedOrderId = orderId;
            this.paymentBalance = balance;
            this.paymentCash = 0;
            this.paymentMethod = '';
            this.paymentReference = '';
            this.showCompletePaymentModal = true;
        },

        submitCompletePayment() {
            if (!this.paymentMethod) { alert('Please select a payment method.'); return; }
            if (this.paymentMethod === 'GCash' && !this.paymentReference.trim()) { alert('Please enter a reference number for GCash.'); return; }

            let cashReceived = parseFloat(this.paymentCash) || 0;
            let currentBalance = parseFloat(this.paymentBalance) || 0;
            let newBalance = Math.max(currentBalance - cashReceived, 0);
            let changeAmount = Math.max(cashReceived - currentBalance, 0);
            let paymentStatus = newBalance === 0 ? 'Fully Paid' : 'Partial';

            const paymentData = {
                order_id: this.selectedOrderId,
                cash: cashReceived,
                balance: newBalance,
                status: paymentStatus,
                change_amount: changeAmount,
                payment_method: this.paymentMethod,
                reference_number: this.paymentMethod === 'GCash' ? this.paymentReference : null
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch('/payments/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.showCompletePaymentModal = false;
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update payment.');
                }
            })
            .catch(err => { console.error(err); alert('An error occurred: ' + err.message); });
        }
    }
}

// ==================== PAGINATION - Initialize after DOM loads ====================
document.addEventListener('DOMContentLoaded', function() {
    const orderRowsPerPage = 5;
    const orderTableBody = document.querySelector('table tbody');
    
    if (!orderTableBody) {
        console.error('❌ Table body not found!');
        return;
    }
    
    const allOrderRows = Array.from(orderTableBody.querySelectorAll('tr.order-row'));
    const orderPaginationLinks = document.getElementById('order-pagination-links');
    const orderPaginationInfo = document.getElementById('order-pagination-info');

    console.log('✅ Found', allOrderRows.length, 'order rows');

    let orderCurrentPage = 1;

    window.getVisibleOrderRows = function() {
        return allOrderRows.filter(row => row.style.display !== 'none');
    }

    window.showOrderPage = function(page) {
        const visibleRows = window.getVisibleOrderRows();
        const orderTotalPages = Math.ceil(visibleRows.length / orderRowsPerPage) || 1;
        
        console.log('📄 Showing page', page, 'with', visibleRows.length, 'visible rows');
        
        orderCurrentPage = page;
        allOrderRows.forEach(row => row.style.display = 'none');
        
        const start = (page - 1) * orderRowsPerPage;
        const end = start + orderRowsPerPage;
        visibleRows.slice(start, end).forEach(row => row.style.display = '');
        
        window.renderOrderPagination(orderTotalPages, visibleRows.length);
    }

    window.renderOrderPagination = function(totalPages, totalResults) {
        orderPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = orderCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
        if (orderCurrentPage !== 1) prev.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showOrderPage(orderCurrentPage - 1); });
        orderPaginationLinks.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === orderCurrentPage ? ' bg-sky-400 text-white' : '');
            li.innerHTML = i === orderCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== orderCurrentPage) li.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showOrderPage(i); });
            orderPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = orderCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
        if (orderCurrentPage !== totalPages) next.querySelector('a')?.addEventListener('click', e => { e.preventDefault(); window.showOrderPage(orderCurrentPage + 1); });
        orderPaginationLinks.appendChild(next);
        
        const start = (orderCurrentPage - 1) * orderRowsPerPage + 1;
        const end = Math.min(orderCurrentPage * orderRowsPerPage, totalResults);
        orderPaginationInfo.textContent = `Showing ${totalResults ? start : 0} to ${end} of ${totalResults} results`;
    }

    window.showOrderPage(1);
});
</script>

@endsection
