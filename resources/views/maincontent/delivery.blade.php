@extends('layouts.app')

@section('title', 'Delivery')

@section('content')
@php
    // Sort deliveries so: For Stock In -> Out for Delivery -> Pending -> others
    $deliveries = $deliveries->sortBy(function ($delivery) {
        $order = [
            'For Stock In'     => 1,
            'Out for Delivery' => 2,
            'Pending'          => 3,
        ];
        return $order[$delivery->status] ?? 99;
    });
@endphp

<div 
    x-data="deliveryComponent()"
    x-cloak 
    class="relative"
>

    <header class="mb-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between border-b pb-3 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Delivery</h1>
        </div>
        <p class="text-gray-600 mt-2">Manage delivery records and item details for each transaction.</p>
    </header>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                   @input="filterDeliveries()"
                   placeholder="Search by Delivery"
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm
                          focus:ring-2 focus:ring-black focus:outline-none w-full md:w-80"
                   style="min-width:200px;" />
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2 whitespace-nowrap">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select x-model="statusFilter" @change="filterDeliveries()"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-black">
                <option value="all">All Status</option>
                <option value="For Stock In">For Stock In</option>
                <option value="Out for Delivery">Out for Delivery</option>
                <option value="Pending">Pending</option>
           </select>
        </div>

    </div>

    <!-- Right: History and Add Button -->
    <div class="flex items-center gap-2">
        @include('added.delivery_history')

        <a href="#" @click.prevent="showAddDelivery = true"
           class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center gap-2
                  hover:bg-yellow-500 transition shadow-md whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14" />
                <path d="M5 12h14" />
            </svg>
            <span>Add New Delivery</span>
        </a>
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
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Product Type</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Requested</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Date Received</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Received By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody id="delivery-table-body" class="divide-y divide-gray-100 relative">
                @forelse ($deliveries->where('status', '!=', 'Delivered') as $delivery)
                    {{-- 
                        Mark newly created delivery with 'is-new' class
                        You can set this in your controller: session(['new_delivery_id' => $delivery->delivery_id]);
                    --}}
                    <tr class="group relative hover:bg-sky-200 cursor-pointer delivery-row {{ session('new_delivery_id') == $delivery->delivery_id ? 'is-new' : '' }}"
                        data-status="{{ $delivery->status }}"
                        data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $delivery->receiver->fname ?? '' }} {{ $delivery->receiver->lname ?? '' }} {{ $delivery->status }}">
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
                            @php
                                $types = $delivery->details->pluck('product_type')->unique()->filter();
                            @endphp
                            @if($types->count() === 1 && $types->first())
                                <span class="text-base font-medium">{{ $types->first() }}</span>
                            @elseif($types->count() > 1)
                                <span class="text-base font-medium">Mixed</span>
                            @else
                                <span class="text-base text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->delivery_date_request }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                            {{ $delivery->status === 'Delivered' ? ($delivery->delivery_date_received ?? '- -') : '- -' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                            @php
                                $dotColor = match($delivery->status) {
                                    'Pending' => 'bg-gray-500',
                                    'Out for Delivery' => 'bg-yellow-500',
                                    'For Stock In' => 'bg-blue-500',
                                    'Delivered' => 'bg-green-500',
                                    default => 'bg-gray-400'
                                };
                            @endphp
                            <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                            <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                        </td>

                        <!-- Hover overlay -->
                        <td colspan="8" class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition-opacity duration-200 bg-sky-100 z-10">
                            @if($delivery->status === 'For Stock In')
                                <div class="w-full h-full flex">
                                    <button type="button" class="flex-1 flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                        @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">Details</span>
                                    </button>

                                    <button type="button"
                                            @click="stockInDelivery({{ $delivery->delivery_id }})"
                                            class="flex-1 flex items-center justify-center bg-green-200 hover:bg-green-300 transition-colors">
                                        <span class="text-green-700 font-semibold text-sm hover:font-bold transition-all duration-200">Stock In</span>
                                    </button>
                                </div>
                            @else
                                <button type="button" class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                    @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails = true">
                                    <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">Details</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <!-- Static empty when there are no deliveries at all -->
                    <tr class="empty-state-delivery-none">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-lg font-medium">No deliveries available</p>
                            <p class="text-sm mt-1">Create a new delivery to get started</p>
                        </td>
                    </tr>
                @endforelse

                @if($deliveries->isNotEmpty())
                <!-- Dynamic empty when filter/search hides all rows -->
                <tr class="empty-state-delivery-filter" style="display: none;">
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-lg font-medium">No deliveries match your filter</p>
                        <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @include('added.add_delivery')

      <!-- Details Modal -->
    <div x-show="showDetails" x-cloak x-transition
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">

        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    Delivery Details - ID: <span x-text="selectedDeliveryId"></span>
                </h2>
            </div>

            <!-- Details Table -->
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
                        @php $grandTotal = $delivery->details->sum(fn($d)=> $d->quantity_product*$d->unit_cost); @endphp
                        @foreach ($delivery->details as $item)
                            <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                                <td class="px-4 py-2 text-center">DD{{ str_pad($item->deliverydetails_id,3,'0',STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->product->product_name ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->quantity_product }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->unit ?? '-' }}</td>
                                <td class="px-4 py-2 text-right">₱{{ number_format($item->unit_cost,2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity_product * $item->unit_cost,2) }}</td>
                            </tr>
                        @endforeach
                        <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₱{{ number_format($grandTotal,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Close Button -->
            <div class="mt-6 flex justify-end">
                <button @click="showDetails = false; showHistoryModal = true"
                        class="bg-yellow-500 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Pagination -->
<div class="custom-pagination mt-6 flex justify-between items-center text-sm text-gray-600 max-w-7xl mx-auto">
    <div id="delivery-pagination-info"></div>
    <ul id="delivery-pagination-links" class="pagination-links flex gap-2"></ul>
</div>

{{-- Move any "new" row (class=is-new) to the top before pagination is computed --}}
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
        if (!confirm("Are you sure you want to stock in this delivery? This will mark it as Delivered.")) return;
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