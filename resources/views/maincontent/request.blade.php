@extends('layouts.app')

@section('title', 'Requests')

@section('content')
@php
    // Sort deliveries so: For Stock In -> Out for Delivery -> Pending -> others
    // NOTE: This sorting logic is preserved from the original PHP
    $deliveries = $deliveries->sortBy(function ($delivery) {
        $order = [
            'For Stock In'     => 1,
            'Out for Delivery' => 2,
            'Pending'          => 3,
        ];
        return $order[$delivery->status] ?? 99;
    });
@endphp

<style>[x-cloak] { display: none !important; }</style>

<div x-data="{
    selectedDeliveryId: null,
    showDetails: false,
    showRequestModal: false,
    // hasPendingAdjustments is now reactive and can be toggled by the bell icon click
    hasPendingAdjustments: {{ ($pendingCount ?? 0) > 0 ? 'true' : 'false' }}, 
    searchQuery: '',
    statusFilter: 'all',
    placeholderIndex: 0,
    placeholders: [
        'Search deliveries'
    ],
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    // The filterDeliveries function is preserved for the search/filter logic
    filterDeliveries() {
        const query = this.searchQuery.toLowerCase().trim();
        const status = this.statusFilter;
        const rows = document.querySelectorAll('#ongoing-deliveries-tbody tr[data-delivery]');
        let hasVisibleRows = false;

        rows.forEach(row => {
            const searchableText = row.getAttribute('data-search').toLowerCase();
            const rowStatus = row.getAttribute('data-status');

            const matchesSearch = !query || searchableText.includes(query);
            const matchesStatus = status === 'all' || rowStatus === status;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                hasVisibleRows = true;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('ongoingDeliveriesEmptyState');
        if (emptyState) {
            emptyState.style.display = hasVisibleRows ? 'none' : '';
        }

        return hasVisibleRows;
    }
}" x-cloak class="relative px-4 sm:px-6 lg:px-8">

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z"/>
                    <path d="M12 17v4"/>
                    <path d="M10 21h4"/>
                </svg>
                Tracking Deliveries
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">View all deliveries that are still in progress, from pending request to stock-in completion.</p>
    </header>

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text"
                    x-model="searchQuery"
                    @input="filterDeliveries()"
                    :placeholder="placeholders[placeholderIndex]"
                    @focus="nextPlaceholder()"
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none transition">

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <label for="status-filter" class="text-sm font-semibold text-gray-700 hidden sm:block">Status:</label>
                <select id="status-filter" x-model="statusFilter" @change="filterDeliveries()"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="For Stock In">For Stock In</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            {{-- Include the Delivery History Button --}}
            @include('added.delivery_history')

            <button type="button"
                    @click="showRequestModal = true; hasPendingAdjustments = false"
                    title="View Stock Adjustments"
                    class="relative inline-flex items-center justify-center p-3 rounded-full 
                           bg-yellow-400 text-gray-900 hover:bg-yellow-500 transition shadow-lg">

                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 16a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2c0-1.5 1-2 1-5a5 5 0 0 1 10 0c0 3 1 3.5 1 5Z" />
                    <path d="M10 18a2 2 0 0 0 4 0" />
                </svg>

                <span x-show="hasPendingAdjustments"
                    x-cloak
                    class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-red-600 border-2 border-white animate-pulse">
                </span>
            </button>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto border-t-4 border-yellow-400">
        <div class="overflow-x-auto">
            <table id="delivery-table" class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Delivery ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Requested By</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Date Requested</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Date Received</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Received By</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>

                <tbody id="ongoing-deliveries-tbody" class="divide-y divide-gray-100">
                    @php
                        $ongoingDeliveries = $deliveries->where('status', '!=', 'Delivered');
                    @endphp

                    @forelse ($ongoingDeliveries as $delivery)
                        <tr
                            class="delivery-row cursor-pointer transition-colors duration-200"
                            data-delivery
                            data-status="{{ $delivery->status }}"
                            data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '-' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $delivery->delivery_date_request }} {{ $delivery->delivery_date_received ?? '- -' }} {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }} {{ $delivery->status }}"
                        >
                            <td class="px-4 py-3 text-center font-semibold text-gray-800">
                                D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-4 py-3 text-left text-gray-600">
                                {{ $delivery->supplier->supplier_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-left text-gray-600">
                                {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $delivery->delivery_date_request }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $delivery->delivery_date_received ?? '- -' }}
                            </td>

                            <td class="px-4 py-3 text-left text-gray-600">
                                {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center flex justify-center items-center space-x-2">
                                @php
                                    $dotColor = match($delivery->status) {
                                        'Pending'          => 'bg-gray-500',
                                        'Out for Delivery' => 'bg-yellow-500',
                                        'For Stock In'     => 'bg-blue-500',
                                        default            => 'bg-gray-400'
                                    };
                                @endphp
                                <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                                <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                            </td>

                            {{-- ACTION BUTTON TO OPEN DETAILS MODAL --}}
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 shadow-sm transition"
                                    title="View details"
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
                        <tr id="ongoingDeliveriesEmptyState">
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-lg font-semibold text-gray-500">No deliveries match your criteria</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting the search query or status filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showDetails" x-transition x-cloak
        class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showDetails = false"
            class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-gray-800">
                    Delivery Details - ID:
                    <span class="text-gray-900"
                        x-text="'D' + selectedDeliveryId.toString().padStart(3, '0')"></span>
                </h2>

                <button
                    type="button"
                    @click="showDetails = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Detail ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Quantity</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600">Unit</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Unit Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($deliveries as $delivery)
                            @php
                                $grandTotal = $delivery->details->sum(fn($d) => $d->quantity_product * $d->unit_cost);
                            @endphp

                            @foreach ($delivery->details as $item)
                                <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-center text-sm font-semibold text-yellow-700">
                                        DD{{ str_pad($item->deliverydetails_id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4 py-2 text-left text-sm text-gray-800">
                                        {{ $item->product->product_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700 font-medium">
                                        {{ $item->quantity_product }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700">
                                        {{ $item->unit ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-700">
                                        ₱{{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-semibold text-gray-900">
                                        ₱{{ number_format($item->quantity_product * $item->unit_cost, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}"
                                class="bg-gray-50/50">
                                <td colspan="5"
                                    class="px-4 py-3 text-right font-bold text-base text-gray-700">
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

            {{-- ACTIONS UNDER THE TABLE --}}
            <div class="mt-6 w-full">
                <div class="mb-2 flex justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">
                        Action
                    </span>
                </div>

                <div class="flex justify-center">
                    @foreach ($deliveries as $delivery)
                        <template x-if="selectedDeliveryId === {{ $delivery->delivery_id }}">
                            @php
                                $isPending         = $delivery->status === 'Pending';
                                $isOutForDelivery = $delivery->status === 'Out for Delivery';
                            @endphp

                            <div class="flex flex-col items-center gap-3">
                                <div class="flex gap-3">
                                    {{-- Confirm Delivery (Pending only) --}}
                                    <button
                                        type="button"
                                        @if ($isPending)
                                            @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'Out for Delivery')"
                                        @endif
                                        class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                            {{ $isPending
                                                ? 'bg-blue-500 text-white hover:bg-blue-600 cursor-pointer'
                                                : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                        @if (!$isPending)
                                            disabled
                                        @endif
                                    >
                                        Confirm Delivery
                                    </button>

                                    {{-- Confirm Stock In (Out for Delivery only) --}}
                                    <button
                                        type="button"
                                        @if ($isOutForDelivery)
                                            @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'For Stock In')"
                                        @endif
                                        class="px-5 py-2 rounded-full text-sm font-semibold shadow-md
                                            {{ $isOutForDelivery
                                                ? 'bg-green-500 text-white hover:bg-green-600 cursor-pointer'
                                                : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                        @if (!$isOutForDelivery)
                                            disabled
                                        @endif
                                    >
                                        Confirm Stock In
                                    </button>
                                </div>

                                <p class="mt-1 text-[11px] text-gray-400">
                                    Current status: {{ $delivery->status }}
                                </p>
                            </div>
                        </template>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div x-show="showRequestModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4">

        <div @click.away="showRequestModal = false" x-transition
                class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    🔔 Pending Stock Adjustments
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase">Stock ID</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase">Product ID</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Product Name</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase">Adjustment Type</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Reason</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Adjusted By</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockAdjustments as $adj)
                            <tr class="text-sm hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-700 font-medium">
                                    S{{ str_pad($adj->stock_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">
                                    P{{ str_pad($adj->stock->product->product_id ?? $adj->stock_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-left text-gray-800 font-medium">
                                    {{ $adj->stock->product->product_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-semibold 
                                    {{ $adj->adjustment_type === 'Addition' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $adj->adjustment_type ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left text-gray-600">
                                    {{ $adj->reason ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left text-gray-700">
                                    {{ $adj->adjustedBy->fname ?? '' }} {{ $adj->adjustedBy->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button type="button"
                                                onclick="handleStockAdjustment({{ $adj->stockadjustment_id }}, 'accept')"
                                                class="px-4 py-1.5 bg-yellow-500 text-gray-900 rounded-full text-xs font-bold hover:bg-yellow-600 transition shadow-sm">
                                            Accept
                                        </button>
                                        <button type="button"
                                                onclick="handleStockAdjustment({{ $adj->stockadjustment_id }}, 'reject')"
                                                class="px-4 py-1.5 bg-gray-200 text-gray-700 rounded-full text-xs font-bold hover:bg-gray-300 transition shadow-sm">
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="7" stroke-width="2" />
                                            <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-500">No pending stock adjustments</p>
                                        <p class="text-sm text-gray-400 mt-1">All adjustment requests have been processed.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button"
                        @click="showRequestModal = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto pb-8">
    <div id="delivery-pagination-info">Showing 0 to 0 of 0 results</div>
    <ul id="delivery-pagination-links" class="pagination-links flex gap-2"></ul>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('ongoing-deliveries-tbody');
    if (!tbody) return;
    const newRow = tbody.querySelector('tr.is-new[data-delivery]');
    if (newRow) {
        tbody.insertBefore(newRow, tbody.firstChild);
    }
});
</script>

<script>
function updateDeliveryStatus(deliveryId, status) {
    if(!confirm(`Are you sure you want to change the status to "${status}"?`)) return;

    axios.post(`/deliveries/${deliveryId}/update-status`, { status })
        .then(res => {
            if(res.data.success) {
                alert(res.data.message);
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error updating status.');
        });
}

// Pagination with filtering
const deliveryRowsPerPage = 5;
const deliveryTableBody = document.getElementById('ongoing-deliveries-tbody');
const allDeliveryRows = Array.from(deliveryTableBody.querySelectorAll('tr[data-delivery]'));
const deliveryPaginationLinks = document.getElementById('delivery-pagination-links');
const deliveryPaginationInfo = document.getElementById('delivery-pagination-info');
const emptyState = document.getElementById('ongoingDeliveriesEmptyState');

let deliveryCurrentPage = 1;
let filteredDeliveryRows = [...allDeliveryRows];

function getFilteredRows() {
    const searchInput = document.querySelector('input[x-model="searchQuery"]');
    const statusFilter = document.querySelector('select[x-model="statusFilter"]');
    
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const status = statusFilter ? statusFilter.value : 'all';
    
    return allDeliveryRows.filter(row => {
        const searchableText = row.getAttribute('data-search').toLowerCase();
        const rowStatus = row.getAttribute('data-status');
        
        const matchesSearch = !query || searchableText.includes(query);
        const matchesStatus = status === 'all' || rowStatus === status;
        
        return matchesSearch && matchesStatus;
    });
}

function showDeliveryPage(page) {
    filteredDeliveryRows = getFilteredRows();
    const deliveryTotalPages = Math.ceil(filteredDeliveryRows.length / deliveryRowsPerPage) || 1;
    
    if (page < 1) page = 1;
    if (page > deliveryTotalPages) page = deliveryTotalPages;
    
    deliveryCurrentPage = page;
    
    // Hide all rows first
    allDeliveryRows.forEach(row => row.style.display = 'none');
    
    // Show empty state if no filtered results
    if (filteredDeliveryRows.length === 0) {
        if (emptyState) emptyState.style.display = '';
        deliveryPaginationInfo.textContent = 'Showing 0 to 0 of 0 results';
        deliveryPaginationLinks.innerHTML = '';
        return;
    } else {
        if (emptyState) emptyState.style.display = 'none';
    }

    // Show only the current page's rows
    const start = (page - 1) * deliveryRowsPerPage;
    const end = start + deliveryRowsPerPage;
    filteredDeliveryRows.slice(start, end).forEach(row => row.style.display = '');

    renderDeliveryPagination(deliveryTotalPages);

    const startItem = filteredDeliveryRows.length ? start + 1 : 0;
    const endItem = end > filteredDeliveryRows.length ? filteredDeliveryRows.length : end;
    deliveryPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${filteredDeliveryRows.length} results`;
}

function renderDeliveryPagination(totalPages) {
    deliveryPaginationLinks.innerHTML = '';

    const prev = document.createElement('li');
    prev.className = 'border rounded px-2 py-1';
    prev.innerHTML = deliveryCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
    if (deliveryCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage - 1); });
    }
    deliveryPaginationLinks.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === deliveryCurrentPage ? ' bg-yellow-400 text-black' : '');
        li.innerHTML = i === deliveryCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== deliveryCurrentPage) {
            li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(i); });
        }
        deliveryPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = deliveryCurrentPage === totalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (deliveryCurrentPage !== totalPages) {
        next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage + 1); });
    }
    deliveryPaginationLinks.appendChild(next);
}

// Trigger pagination update when filters change
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[x-model="searchQuery"]');
    const statusFilter = document.querySelector('select[x-model="statusFilter"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            deliveryCurrentPage = 1;
            showDeliveryPage(1);
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            deliveryCurrentPage = 1;
            showDeliveryPage(1);
        });
    }
    
    showDeliveryPage(1);
});
</script>

<script>
function handleStockAdjustment(id, action) {
    if (action === 'accept' && !confirm('Approve this stock adjustment?')) return;
    if (action === 'reject' && !confirm('Reject this stock adjustment?')) return;

    fetch(`{{ url('/stock-adjustments') }}/${id}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Done');
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error processing adjustment.');
    });
}
</script>

@endsection