@extends('layouts.app')

@section('title', 'Requests')

@section('content')
<div x-data="{ 
    selectedDeliveryId: null, 
    showDetails: false,
    showRequestModal: false,
    hasPendingAdjustments: {{ ($pendingCount ?? 0) > 0 ? 'true' : 'false' }},
    searchQuery: '',
    placeholderIndex: 0,
    placeholders: [
        'Search Deliveries',
        'Search ID',
        'Supplier',
        'Requested By',
        'Request Date',
        'Status'
    ],
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterDeliveries() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#ongoing-deliveries-tbody tr[data-delivery]');
        let hasVisibleRows = false;
        
        rows.forEach(row => {
            if (!query) {
                row.style.display = '';
                hasVisibleRows = true;
            } else {
                const searchableText = row.getAttribute('data-search').toLowerCase();
                if (searchableText.includes(query)) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        const emptyState = document.getElementById('ongoingDeliveriesEmptyState');
        if (emptyState) {
            emptyState.style.display = hasVisibleRows ? 'none' : '';
        }
        
        return hasVisibleRows;
    }
}" x-cloak class="relative">

    <!-- Page Header -->
    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Tracking Deliveries</h1>
        </div>
        <p class="text-gray-600 mt-2">View all deliveries that are still in progress.</p>
    </header>
    <br>

    <!-- Search Bar and Actions -->
    <div class="flex justify-between items-center mb-4 gap-2 max-w-7xl mx-auto">
        <!-- Search Bar (Left) -->
        <div class="relative w-full max-w-xs">
            <input type="text"
                   x-model="searchQuery"
                   @input="filterDeliveries()"
                   :placeholder="placeholders[placeholderIndex]"
                   class="w-full pl-8 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">

            <!-- Search Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
        </div>

       <!-- Right side: History + New Request -->
        <div class="flex items-center gap-2">
            @include('added.delivery_history')

            <!-- Pending Request / Bell Button -->
            <button type="button"
                    @click="showRequestModal = true; hasPendingAdjustments = false"
                    class="relative inline-flex items-center justify-center px-4 py-2 rounded-xl
                        bg-gray-200 hover:bg-gray-300 transition shadow-md">

                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 16a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2c0-1.5 1-2 1-5a5 5 0 0 1 10 0c0 3 1 3.5 1 5Z" />
                    <path d="M10 18a2 2 0 0 0 4 0" />
                </svg>

                <!-- Red indicator dot -->
                <span x-show="hasPendingAdjustments"
                    x-cloak
                    class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-red-500 border-2 border-white">
                </span>
            </button>
        </div>
    </div>

    <!-- Delivery Table -->
    <div class="bg-white p-6 rounded-xl shadow max-w-full mx-auto overflow-x-auto">
        <table id="delivery-table" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Delivery ID</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Supplier</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Requested By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Requested</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Received</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody id="ongoing-deliveries-tbody" class="divide-y divide-gray-100">
                @forelse ($deliveries->where('status', '!=', 'Delivered') as $delivery)
                    <tr class="group relative hover:bg-sky-200 cursor-pointer" data-delivery
                        data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '-' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $delivery->delivery_date_request }} {{ $delivery->delivery_date_received ?? '- -' }} {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }} {{ $delivery->status }}">
                        <!-- Normal row content -->
                        <td class="px-4 py-3 text-center font-medium text-gray-800 group-hover:opacity-0">
                            D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->supplier->supplier_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->delivery_date_request }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->delivery_date_received ?? '- -' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                            @php
                                $dotColor = match($delivery->status) {
                                    'In Transit' => 'bg-gray-500',
                                    'Out for Delivery' => 'bg-yellow-500',
                                    'For Stock In' => 'bg-blue-500',
                                    'Delivered' => 'bg-green-500',
                                    default => 'bg-gray-400'
                                };
                            @endphp
                            <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                            <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                        </td>

                        <!-- Hover overlay for whole row -->
                        <td colspan="7" class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition-opacity duration-200 bg-sky-100 z-10">
                            <div class="w-full h-full flex">
                                <!-- Details button -->
                                <button type="button"
                                        class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                        @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                                    <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                        Details
                                    </span>
                                </button>

                                @if($delivery->status === 'Pending')
                                    <button type="button"
                                            class="flex-1 flex items-center justify-center bg-blue-200 hover:bg-blue-300 transition-colors"
                                            @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'Out for Delivery')">
                                        <span class="text-blue-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Confirm to Delivery
                                        </span>
                                    </button>
                                @elseif($delivery->status === 'Out for Delivery')
                                    <button type="button"
                                            class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors"
                                            @click="updateDeliveryStatus({{ $delivery->delivery_id }}, 'For Stock In')">
                                        <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Confirm to Stock In
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Static Empty State - No Data in Database -->
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-500">No ongoing deliveries found</p>
                                <p class="text-sm text-gray-400 mt-1">There are currently no deliveries in progress</p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                <!-- Dynamic Empty State - Search/Filter No Results -->
                <tr id="ongoingDeliveriesEmptyState" style="display: none;">
                    <td colspan="7" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-500">No deliveries match your filter</p>
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your search or filter criteria</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Delivery Details Modal -->
    <div x-show="showDetails" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">
                Delivery Details - ID: <span x-text="selectedDeliveryId"></span>
            </h2>

            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Detail ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($deliveries as $delivery)
                        @php
                            $grandTotal = $delivery->details->sum(fn($d) => $d->quantity_product * $d->unit_cost);
                        @endphp

                        @foreach ($delivery->details as $item)
                            <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                                <td class="px-4 py-2 text-center">
                                    DD{{ str_pad($item->deliverydetails_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-2 text-center">{{ $item->product->product_name ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->quantity_product }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->unit ?? '-' }}</td>
                                <td class="px-4 py-2 text-right">₱{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity_product * $item->unit_cost, 2) }}</td>
                            </tr>
                        @endforeach

                        <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₱{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6 flex justify-end">
                <button @click="showDetails = false"
                        class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

<!-- Stock Adjustments Modal (centered) -->
<div x-show="showRequestModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div x-transition
         class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Stock Adjustments</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Stock ID</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Product ID</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Product Name</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Adjustment Type</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Reason</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Adjusted By</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stockAdjustments as $adj)
                        <tr class="text-base">
                            <td class="px-4 py-3 text-center text-gray-700">
                                S{{ str_pad($adj->stock_id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                P{{ str_pad($adj->stock->product->product_id ?? $adj->stock_id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $adj->stock->product->product_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $adj->adjustment_type ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $adj->reason ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $adj->adjustedBy->fname ?? '' }} {{ $adj->adjustedBy->lname ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    <button type="button"
                                            onclick="handleStockAdjustment({{ $adj->stockadjustment_id }}, 'accept')"
                                            class="px-3 py-1.5 bg-yellow-500 text-white rounded text-sm font-semibold hover:bg-yellow-600">
                                        Accept
                                    </button>
                                    <button type="button"
                                            onclick="handleStockAdjustment({{ $adj->stockadjustment_id }}, 'reject')"
                                            class="px-3 py-1.5 bg-gray-300 text-gray-800 rounded text-sm font-semibold hover:bg-gray-400">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="7" stroke-width="2" />
                                        <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">No pending stock adjustments</p>
                                    <p class="text-sm text-gray-400 mt-1">All adjustment requests have been processed.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end">
            <button type="button"
                    @click="showRequestModal = false"
                    class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-100">
                Close
            </button>
        </div>
    </div>
</div>

</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="delivery-pagination-info">Showing 0 to 0 of 0 results</div>
    <ul id="delivery-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

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

// Pagination
const deliveryRowsPerPage = 5;
const deliveryTableBody = document.getElementById('ongoing-deliveries-tbody');
const deliveryRows = Array.from(deliveryTableBody.querySelectorAll('tr[data-delivery]'));
const deliveryPaginationLinks = document.getElementById('delivery-pagination-links');
const deliveryPaginationInfo = document.getElementById('delivery-pagination-info');

let deliveryCurrentPage = 1;
const deliveryTotalPages = Math.ceil(deliveryRows.length / deliveryRowsPerPage) || 1;

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
    if (deliveryCurrentPage !== 1) {
        prev.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage - 1); });
    }
    deliveryPaginationLinks.appendChild(prev);

    for (let i = 1; i <= deliveryTotalPages; i++) {
        const li = document.createElement('li');
        li.className = 'border rounded px-2 py-1' + (i === deliveryCurrentPage ? ' bg-sky-400 text-white' : '');
        li.innerHTML = i === deliveryCurrentPage ? i : `<a href="#">${i}</a>`;
        if (i !== deliveryCurrentPage) {
            li.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(i); });
        }
        deliveryPaginationLinks.appendChild(li);
    }

    const next = document.createElement('li');
    next.className = 'border rounded px-2 py-1';
    next.innerHTML = deliveryCurrentPage === deliveryTotalPages ? 'Next »' : `<a href="#">Next »</a>`;
    if (deliveryCurrentPage !== deliveryTotalPages) {
        next.querySelector('a').addEventListener('click', e => { e.preventDefault(); showDeliveryPage(deliveryCurrentPage + 1); });
    }
    deliveryPaginationLinks.appendChild(next);
}

showDeliveryPage(1);
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
