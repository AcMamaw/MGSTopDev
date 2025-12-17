@extends('layouts.app')

@section('title', 'Delivery')

@section('content')
@php
    $deliveries = $deliveries->sortBy(function ($delivery) {
        $order = [
            'For Stock In'     => 1,
            'Out for Delivery' => 2,
            'Pending'          => 3,
        ];
        return $order[$delivery->status] ?? 99;
    });
@endphp

<style>[x-cloak]{display:none!important}</style>

<div
    x-data="{
        selectedDeliveryId: null,
        showDetails: false,
        showAddDelivery: false,
        showHistoryModal: false,
        searchQuery: '',
        statusFilter: 'all',
        placeholderIndex: 0,
        placeholders: ['Search by delivery'],

        nextPlaceholder() {
            this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
        },

        filterDeliveries() {
            const query  = this.searchQuery.toLowerCase().trim();
            const status = this.statusFilter;
            const rows   = document.querySelectorAll('#delivery-table-body tr.delivery-row');
            let hasVisibleRows = false;

            rows.forEach(row => {
                const searchableText = row.getAttribute('data-search').toLowerCase();
                const rowStatus      = row.getAttribute('data-status');

                const matchesSearch = !query || searchableText.includes(query);
                const matchesStatus = status === 'all' || rowStatus === status;

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });

            const filterEmptyState  = document.querySelector('.empty-state-delivery-filter');
            const initialEmptyState = document.querySelector('.empty-state-delivery-none');

            if (initialEmptyState && initialEmptyState.parentElement.children.length === 1) {
                // only initial empty state present; leave it
            } else if (filterEmptyState) {
                filterEmptyState.style.display = hasVisibleRows ? 'none' : '';
            }

            // if you paginate in JS, re‑init here
            if (typeof initializeDeliveryPagination === 'function') {
                initializeDeliveryPagination();
            }
        }
    }"
    x-cloak
    class="relative px-4 sm:px-6 lg:px-8"
>
    {{-- HEADER --}}
    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-yellow-400">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-yellow-500" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z"/>
                    <path d="M12 17v4"/>
                    <path d="M10 21h4"/>
                </svg>
                Delivery
            </h1>
        </div>
        <p class="text-gray-600 mt-2 text-md">
            Manage delivery records and item details for each transaction.
        </p>
    </header>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-7xl mx-auto">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-7xl mx-auto">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CONTROLS --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full sm:w-80">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="filterDeliveries()"
                    :placeholder="placeholders[placeholderIndex]"
                    @focus="nextPlaceholder()"
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
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label for="status-filter" class="text-sm font-semibold text-gray-700 hidden sm:block">
                    Status:
                </label>
                <select
                    id="status-filter"
                    x-model="statusFilter"
                    @change="filterDeliveries()"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer"
                >
                    <option value="all">All Status</option>
                    <option value="For Stock In">For Stock In</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div>

        {{-- Right side actions --}}
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            @include('added.delivery_history')

            <a href="#"
               @click.prevent="showAddDelivery = true"
               class="bg-yellow-400 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center gap-2 hover:bg-yellow-500 transition shadow-lg whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="20" height="20" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>New Delivery</span>
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-full mx-auto overflow-x-auto border-t-4 border-yellow-400">
        <table id="delivery-table" class="min-w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Delivery ID</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Supplier</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Requested By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Product Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Date Requested</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Date Received</th>
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-600 tracking-wider">Received By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-600 tracking-wider">Action</th>
                </tr>
            </thead>

            <tbody id="delivery-table-body" class="divide-y divide-gray-100 relative">
                @forelse ($deliveries->where('status', '!=', 'Delivered') as $delivery)
                    <tr
                        class="delivery-row cursor-pointer transition-colors duration-200 {{ session('new_delivery_id') == $delivery->delivery_id ? 'is-new bg-yellow-50' : '' }}"
                        data-status="{{ $delivery->status }}"
                        data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $delivery->receiver->fname ?? '' }} {{ $delivery->receiver->lname ?? '' }} {{ $delivery->status }}"
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
                            @php
                                $types = $delivery->details->pluck('product_type')->unique()->filter();
                            @endphp
                            @if ($types->count() === 1 && $types->first())
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full">
                                    {{ $types->first() }}
                                </span>
                            @elseif($types->count() > 1)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full">Mixed</span>
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $delivery->delivery_date_request }}
                        </td>

                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $delivery->status === 'Delivered' ? ($delivery->delivery_date_received ?? '- -') : '- -' }}
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
                                'Re Stock'         => 'bg-blue-500',
                                'Delivered'        => 'bg-green-500',
                                default            => 'bg-gray-400',
                            };
                        @endphp
                        <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                        <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                    </td>

                        {{-- Action button (no hover overlay) --}}
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 shadow-sm transition"
                                title="View details"
                            >
                                {{-- right arrow icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-state-delivery-none">
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-semibold text-gray-500">No deliveries available</p>
                                <p class="text-sm text-gray-400 mt-1">Create a new delivery to get started.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                @if($deliveries->isNotEmpty())
                    <tr class="empty-state-delivery-filter" style="display:none;">
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p class="text-lg font-semibold text-gray-500">No deliveries match your filter</p>
                                <p class="text-sm text-gray-400 mt-1">Try adjusting the search query or status filter.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>


    @include('added.add_delivery')

    {{-- DETAILS MODAL --}}
    <div x-show="showDetails" x-transition x-cloak
        class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showDetails = false"
            class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            {{-- HEADER WITH CLOSE BUTTON ON RIGHT --}}
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
                                <td colspan="5" class="px-4 py-3 text-right font-bold text-base text-gray-700">
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
                {{-- Label centered --}}
                <div class="mb-2 flex justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">
                        Action
                    </span>
                </div>
                    
                {{-- Button centered --}}
                <div class="flex justify-center">
                    @foreach ($deliveries as $delivery)
                        <template x-if="selectedDeliveryId === {{ $delivery->delivery_id }}">
                            @php
                                $isForStockIn  = $delivery->status === 'For Stock In';
                                $isReStock     = $delivery->status === 'Re Stock';
                                $canStockIn    = $isForStockIn || $isReStock;
                            @endphp

                            <div class="text-center">
                                <button
                                    type="button"
                                    @if ($canStockIn)
                                        @click="stockInDelivery({{ $delivery->delivery_id }})"
                                    @endif
                                    class="px-6 py-2 rounded-full text-sm font-semibold shadow-md
                                        {{ $canStockIn
                                            ? 'bg-green-500 text-black hover:bg-green-600 cursor-pointer'
                                            : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                                    @if (!$canStockIn)
                                        disabled
                                    @endif
                                >
                                    Stock In
                                </button>

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
</div>

{{-- PAGINATION WRAPPER --}}
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto px-4 sm:px-0">
    <div id="delivery-pagination-info"></div>
    <ul id="delivery-pagination-links" class="pagination-links flex gap-2"></ul>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('delivery-table-body');
    if (!tbody) return;
    
    const newRow = tbody.querySelector('tr.is-new.delivery-row');
    if (newRow) {
        // Move to top
        tbody.insertBefore(newRow, tbody.firstChild);
        
        // After 3 seconds, remove the is-new class and move back to sorted position
        setTimeout(() => {
            newRow.classList.remove('is-new');
            
            // Find correct sorted position based on status
            const newRowStatus = newRow.getAttribute('data-status');
            const statusOrder = {
                'For Stock In': 1,
                'Out for Delivery': 2,
                'Pending': 3
            };
            const newRowOrder = statusOrder[newRowStatus] ?? 99;
            
            const allRows = Array.from(tbody.querySelectorAll('tr.delivery-row'));
            let insertBeforeRow = null;
            
            for (let row of allRows) {
                if (row === newRow) continue;
                const rowStatus = row.getAttribute('data-status');
                const rowOrder = statusOrder[rowStatus] ?? 99;
                
                if (rowOrder > newRowOrder) {
                    insertBeforeRow = row;
                    break;
                }
            }
            
            if (insertBeforeRow) {
                tbody.insertBefore(newRow, insertBeforeRow);
            } else {
                tbody.appendChild(newRow);
            }
            
            // Reinitialize pagination after moving
            initializePagination();
        }, 3000);
    }
});
</script>

<!-- Scripts -->
<script>
    async function stockInDelivery(deliveryId) {
        if (!confirm("Are you sure you want to stock in this delivery?")) return;
        try {
            const response = await axios.post(`/deliveries/${deliveryId}/stock-in`);
            if (response.data.success) {
                alert(response.data.message);
                location.reload();
            }
        } catch (error) {
            console.error(error);
            alert('Error stocking in delivery.');
        }
    }

    function deliveryComponent() {
        return {
            showDetails: false,
            selectedDeliveryId: null,
            showAddDelivery: false,
            showHistoryModal: false,
            searchQuery: '',
            statusFilter: 'all',

            filterDeliveries() {
                const rows = document.querySelectorAll('.delivery-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    const searchText = row.getAttribute('data-search').toLowerCase();
                    const query = this.searchQuery.toLowerCase();

                    const matchesStatus = this.statusFilter === 'all' || status === this.statusFilter;
                    const matchesSearch = !query || searchText.includes(query);

                    if (matchesStatus && matchesSearch) {
                        row.style.display = '';          // logically visible
                        visibleCount++;
                    } else {
                        row.style.display = 'none';      // logically hidden
                    }
                });

                const emptyStateFilter = document.querySelector('.empty-state-delivery-filter');
                if (emptyStateFilter) {
                    emptyStateFilter.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }

                // Rebuild pagination using only currently visible rows
                initializePagination();
            }
        }
    }

    // Pagination
    let deliveryRowsPerPage = 5;
    let deliveryTableBody, deliveryRows, deliveryPaginationLinks, deliveryPaginationInfo;
    let deliveryCurrentPage = 1;
    let deliveryTotalPages = 1;

    function initializePagination() {
        deliveryTableBody = document.getElementById('delivery-table-body');
        // Only rows that are currently visible (not style.display = 'none')
        deliveryRows = Array.from(deliveryTableBody.querySelectorAll('.delivery-row'))
            .filter(row => row.style.display !== 'none');

        deliveryPaginationLinks = document.getElementById('delivery-pagination-links');
        deliveryPaginationInfo = document.getElementById('delivery-pagination-info');
        
        deliveryCurrentPage = 1;
        deliveryTotalPages = Math.ceil(deliveryRows.length / deliveryRowsPerPage) || 1;
        
        showDeliveryPage(1);
    }


    function showDeliveryPage(page) {
        deliveryCurrentPage = page;
        deliveryRows.forEach(row => row.style.display = 'none');
        const start = (page - 1) * deliveryRowsPerPage;
        const end = start + deliveryRowsPerPage;
        deliveryRows.slice(start, end).forEach(row => row.style.display = '');
        renderDeliveryPagination();
        const startItem = deliveryRows.length ? start + 1 : 0;
        const endItem = end > deliveryRows.length ? deliveryRows.length : end;
        deliveryPaginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${deliveryRows.length} results`;
    }

    function renderDeliveryPagination() {
        deliveryPaginationLinks.innerHTML = '';

        const prev = document.createElement('li');
        prev.className = 'border rounded px-2 py-1';
        prev.innerHTML = deliveryCurrentPage === 1 ? '« Prev' : `<a href="#">« Prev</a>`;
        if (deliveryCurrentPage !== 1) prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage - 1); });
        deliveryPaginationLinks.appendChild(prev);

        for (let i = 1; i <= deliveryTotalPages; i++) {
            const li = document.createElement('li');
            li.className = 'border rounded px-2 py-1' + (i === deliveryCurrentPage ? ' bg-yellow-400 text-black' : '');
            li.innerHTML = i === deliveryCurrentPage ? i : `<a href="#">${i}</a>`;
            if (i !== deliveryCurrentPage) li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(i); });
            deliveryPaginationLinks.appendChild(li);
        }

        const next = document.createElement('li');
        next.className = 'border rounded px-2 py-1';
        next.innerHTML = deliveryCurrentPage === deliveryTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
        if (deliveryCurrentPage !== deliveryTotalPages) next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage + 1); });
        deliveryPaginationLinks.appendChild(next);
    }

    // Initialize pagination on page load
    initializePagination();
</script>
@endsection