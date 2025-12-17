@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="paymentComponent()">
  <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
                Sales Transaction 
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">Manage purchase orders and their details for each transaction.</p>
    </header>

<!-- Controls -->
<div class="max-w-7xl mx-auto mb-6 flex items-center justify-between gap-4">

    <!-- Left: Search and Filter -->
    <div class="flex items-center gap-3">
        
        <!-- Search Input -->
        <div class="relative">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            <input type="text"
                   x-model="searchQuery"
                   @input="filterOrders()"
                   placeholder="Search by Order ID"
                   class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none transition">
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2 whitespace-nowrap">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select x-model="statusFilter" @change="filterOrders()"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
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
                class="bg-yellow-400 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center gap-2
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

    </div>
</div>

<!-- Purchase Orders Table -->
<div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
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
                <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 relative">
            @forelse ($orders as $order)
                @if ($order->status !== 'Completed')
                    @php 
                        $payStatus    = $order->payment->status ?? 'Not Paid';
                        $orderStatus  = $order->status;
                        $isPartial    = $payStatus === 'Partial';
                        $isFullyPaid  = $payStatus === 'Fully Paid';
                        $balance      = $order->payment->balance ?? $order->total_amount;
                    @endphp

                    <tr class="order-row cursor-pointer transition-colors duration-200"
                        data-status="{{ $order->status }}"
                        data-search="O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }} {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }} {{ $order->status }}">
                        <!-- Order ID -->
                        <td class="px-4 py-3 text-center font-medium text-gray-800">
                            O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        <!-- Customer Name -->
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                        </td>

                        <!-- Category Name -->
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $order->category->category_name ?? 'N/A' }}
                        </td>

                        <!-- Product Type -->
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $order->product_type ?? 'N/A' }}
                        </td>

                        <!-- Order Date -->
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                        </td>

                        <!-- Total Amount -->
                        <td class="px-4 py-3 text-center text-gray-600">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>

                        <!-- Balance -->
                        <td class="px-4 py-3 text-center">
                            ₱{{ number_format($balance, 2) }}
                        </td>

                        <!-- Payment Status -->
                        <td class="px-4 py-3 text-center">
                            {{ $payStatus }}
                        </td>

                        <!-- Order Status -->
                        <td class="px-4 py-3 text-center flex justify-center items-center space-x-2">
                            @php
                                $dotColor = match($order->status) {
                                    'Pending'     => 'bg-gray-500',
                                    'In Progress' => 'bg-yellow-500',
                                    'Released'    => 'bg-blue-500',
                                    'Completed'   => 'bg-green-500',
                                    'Cancelled'   => 'bg-red-500',
                                    default       => 'bg-gray-400'
                                };
                            @endphp
                            <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                            <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                        </td>

                        <!-- Action button (opens details / actions modal) -->
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                @click="
                                    selectedOrderId = {{ $order->order_id }};
                                    showOrderDetails = true
                                "
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 shadow-sm transition"
                                title="View actions"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endif
            @empty
                <tr class="empty-state-no-orders">
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-lg font-medium">No orders available</p>
                        <p class="text-sm mt-1">Create a new order to get started</p>
                    </td>
                </tr>
            @endforelse

            <!-- Dynamic empty state when filter returns no results -->
            @if($orders->isNotEmpty())
                <tr class="empty-state-filter" style="display: none;">
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
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
    <div id="order-pagination-info"></div>
    <ul id="order-pagination-links" class="pagination-links flex gap-2"></ul>
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

            <button
                type="button"
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
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Product</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Size</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Color</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        @php
                            $grandTotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                            $payStatus  = $order->payment->status ?? 'Not Paid';
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

        {{-- ACTIONS: ASSIGN / COMPLETE PAYMENT / FOR CLAIM --}}
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
                            $payStatus   = $order->payment->status ?? 'Not Paid';
                            $orderStatus = $order->status;
                            $isPartial   = $payStatus === 'Partial';
                            $isFullyPaid = $payStatus === 'Fully Paid';
                            $balance     = $order->payment->balance ?? $order->total_amount;
                        @endphp

                        <div class="flex flex-col items-center gap-3">
                            <div class="flex flex-wrap justify-center gap-3">
                                {{-- Assign Job Order: active when Pending --}}
                                <button
                                    type="button"
                                    @if ($orderStatus === 'Pending')
                                        @click="
                                            showOrderDetails = false;
                                            openAssignJobOrder({{ $order->order_id }})
                                        "
                                    @endif
                                    class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                        {{ $orderStatus === 'Pending'
                                            ? 'bg-purple-500 text-black hover:bg-purple-600 cursor-pointer'
                                            : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                    @if ($orderStatus !== 'Pending')
                                        disabled
                                    @endif
                                >
                                    Assign Job Order
                                </button>

                                {{-- Complete Payment: clickable when Partial; disabled otherwise --}}
                                <button
                                    type="button"
                                    @if ($isPartial)
                                        @click="
                                            showOrderDetails = false;
                                            openPaymentModal({{ $order->order_id }}, {{ $balance }})
                                        "
                                    @endif
                                    class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                        {{ $isPartial
                                            ? 'bg-yellow-500 text-gray-900 hover:bg-yellow-600 cursor-pointer'
                                            : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                    @if (!$isPartial)
                                        disabled
                                    @endif
                                >
                                    Complete Payment
                                </button>

                                {{-- For Claim / Ready to Claim: clickable only when status Released AND Fully Paid --}}
                                @php
                                    $canClaim = $orderStatus === 'Released' && $isFullyPaid;
                                @endphp
                                <button
                                    type="button"
                                    @if ($canClaim)
                                        @click="markAsCompleted({{ $order->order_id }})"
                                    @endif
                                    class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                        {{ $canClaim
                                            ? 'bg-green-500 text-black hover:bg-green-600 cursor-pointer'
                                            : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                    @if (!$canClaim)
                                        disabled
                                    @endif
                                >
                                    For Claim
                                </button>
                            </div>

                            <p class="mt-1 text-[11px] text-gray-400">
                                Order status: {{ $orderStatus }} &mdash; Payment status: {{ $payStatus }}
                            </p>
                        </div>
                    </template>
                @endforeach
            </div>
        </div>
    </div>
</div>

        @include('added.payment_complete')
        @include('added.assign_joborders')

<script>
const ORDER_STATUS_PRIORITY = {
    'Released': 1,
    'In Progress': 2,
    'Pending': 3,
    '': 99
};

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

        // ===== RECEIPT STATE (NEW) =====
        showReceipt: false,
        showSuccess: false,
        receipt: {
            receipt_number: null,
            items: [],
            status: '',
            customer_name: '',
            customer_address: '',
            payment_method: '',
            reference_number: null,
            payment_date: '',
            amount: 0,
            cash: 0,
            change_amount: 0,
            balance: 0,
        },

        printReceipt() {
            window.print();
        },

        colorNameFromHex(hex) {
            return hex || '';
        },

        // Filter orders (set logical visibility only)
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
                    row.dataset.visible = "true";
                    visibleCount++;
                } else {
                    row.dataset.visible = "false";
                }
            });

            const emptyStateFilter = document.querySelector('.empty-state-filter');
            if (emptyStateFilter) {
                emptyStateFilter.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
            }

            if (window.showOrderPage) {
                window.showOrderPage(1);
            }
        },

        // Assign Job Order Methods (unchanged)
        async openAssignJobOrder(orderId) {
            this.selectedOrderId = orderId;
            this.showAssignJobOrderModal = true;
            this.selectedEmployees = [];
            this.selectAllEmployees = false;
            this.employees = [];
            this.employeeSearch = '';

            try {
                const res = await fetch('/employees/active', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) {
                    const errorText = await res.text();
                    alert('Failed to load employees: ' + res.statusText);
                    this.employees = [];
                    return;
                }

                const data = await res.json();
                if (data.employees && Array.isArray(data.employees)) {
                    this.employees = data.employees;
                } else {
                    this.employees = [];
                    alert('Invalid employee data format received');
                }
            } catch (err) {
                console.error('Error fetching employees:', err);
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
            } else {
                this.selectedEmployees = [];
            }
        },

        submitAssignJobOrder() {
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
            .then(res => res.json())
            .then(data => {
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
                console.error('Assign job order error:', err);
                alert('An error occurred while assigning job order: ' + err.message);
            });
        },

        // Payment Methods
        openPaymentModal(orderId, balance) {
            this.selectedOrderId = orderId;
            this.paymentBalance = balance;
            this.paymentCash = 0;
            this.paymentMethod = '';
            this.paymentReference = '';
            this.showCompletePaymentModal = true;
        },

        submitCompletePayment() {
            if (!this.paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            if (this.paymentMethod === 'GCash' && !this.paymentReference.trim()) {
                alert('Please enter a reference number for GCash.');
                return;
            }

            const cashReceived   = Number(this.paymentCash) || 0;
            const currentBalance = Number(this.paymentBalance) || 0;
            const newBalance     = Math.max(currentBalance - cashReceived, 0);
            const changeAmount   = Math.max(cashReceived - currentBalance, 0);
            const paymentStatus  = newBalance === 0 ? 'Fully Paid' : 'Partial';

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
                if (!data.success) {
                    alert(data.message || 'Failed to update payment.');
                    return;
                }

                const pay   = data.payment || {};
                const order = data.order   || {};

                // Fill minimal receipt info
                this.receipt.receipt_number   = pay.payment_id || pay.id || null;
                this.receipt.status           = pay.status || paymentStatus;
                this.receipt.payment_method   = pay.payment_method || this.paymentMethod;
                this.receipt.reference_number = pay.reference_number || this.paymentReference || null;
                this.receipt.payment_date     = pay.payment_date || new Date().toISOString().slice(0, 10);
                this.receipt.amount           = Number(pay.amount ?? currentBalance);
                this.receipt.cash             = Number(pay.cash ?? cashReceived);
                this.receipt.change_amount    = Number(pay.change_amount ?? changeAmount);
                this.receipt.balance          = Number(pay.balance ?? newBalance);

                this.receipt.customer_name    = order.customer_name || '';
                this.receipt.customer_address = order.customer_address || '';
                this.receipt.items            = Array.isArray(order.items) ? order.items : [];

                this.showCompletePaymentModal = false;
                this.showReceipt = true;
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred: ' + err.message);
            });
        }
    }
}

// ==================== PAGINATION + INITIAL ORDER ====================
document.addEventListener('DOMContentLoaded', function() {
    const orderRowsPerPage   = 5;
    const orderTableBody     = document.querySelector('#order-table tbody');
    const orderPaginationLinks = document.getElementById('order-pagination-links');
    const orderPaginationInfo  = document.getElementById('order-pagination-info');

    if (!orderTableBody || !orderPaginationLinks || !orderPaginationInfo) {
        return;
    }

    let orderCurrentPage = 1;

    // Sort rows once by status (Released → In Progress → Pending)
    function orderInitialRows() {
        const rows = Array.from(orderTableBody.querySelectorAll('.order-row'));
        rows.forEach(r => { if (!r.dataset.visible) r.dataset.visible = "true"; });

        rows.sort((a, b) => {
            const sa = a.getAttribute('data-status') || '';
            const sb = b.getAttribute('data-status') || '';
            const pa = ORDER_STATUS_PRIORITY[sa] ?? 99;
            const pb = ORDER_STATUS_PRIORITY[sb] ?? 99;

            if (pa !== pb) return pa - pb;

            const ca = a.getAttribute('data-created-at') || '';
            const cb = b.getAttribute('data-created-at') || '';
            return ca.localeCompare(cb);
        });

        rows.forEach(r => orderTableBody.appendChild(r));
    }

    // Get rows that are logically visible (for pagination)
    window.getVisibleOrderRows = function() {
        return Array.from(orderTableBody.querySelectorAll('.order-row'))
            .filter(row => row.dataset.visible !== "false");
    };

    // Show a page
    window.showOrderPage = function(page) {
        const visibleRows    = window.getVisibleOrderRows();
        const totalResults   = visibleRows.length;
        const orderTotalPages = Math.ceil(totalResults / orderRowsPerPage) || 1;

        if (page < 1) page = 1;
        if (page > orderTotalPages) page = orderTotalPages;

        orderCurrentPage = page;

        Array.from(orderTableBody.querySelectorAll('.order-row')).forEach(row => {
            row.style.display = 'none';
        });

        const start = (page - 1) * orderRowsPerPage;
        const end   = start + orderRowsPerPage;
        visibleRows.slice(start, end).forEach(row => {
            row.style.display = '';
        });

        window.renderOrderPagination(orderTotalPages, totalResults);
    };

    // Render pagination buttons + info
    window.renderOrderPagination = function(totalPages, totalResults) {
        orderPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = orderCurrentPage === 1 ? '« Prev' : '<a href="#">« Prev</a>';
        if (orderCurrentPage !== 1) {
            prev.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                window.showOrderPage(orderCurrentPage - 1);
            });
        }
        orderPaginationLinks.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === orderCurrentPage ? ' bg-yellow-400 text-black' : '');
            li.innerHTML = i === orderCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== orderCurrentPage) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    window.showOrderPage(i);
                });
            }
            orderPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = orderCurrentPage === totalPages ? 'Next »' : '<a href="#">Next »</a>';
        if (orderCurrentPage !== totalPages) {
            next.querySelector('a').addEventListener('click', e => {
                e.preventDefault();
                window.showOrderPage(orderCurrentPage + 1);
            });
        }
        orderPaginationLinks.appendChild(next);

        const start = (orderCurrentPage - 1) * orderRowsPerPage + 1;
        const end   = Math.min(orderCurrentPage * orderRowsPerPage, totalResults);
        orderPaginationInfo.textContent =
            `Showing ${totalResults ? start : 0} to ${end} of ${totalResults} results`;
    };

    orderInitialRows();
    window.showOrderPage(1);
});
</script>
@endsection

