<div x-data="{ 
    showHistoryModal: false, 
    showDetails2: false, 
    selectedDeliveryId: null, 
    searchQuery: '',
    placeholderIndex: 0,
    placeholders: [
        'Search Deliveries',
        'Search ID',
        'Supplier',
        'Received By',
        'Requested By',
        'Request Date',
        'Received Date'
    ],
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterDeliveries() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#deliveriesTableBody tr[data-delivery]');
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
        
        // Show/hide empty state
        const emptyState = document.getElementById('deliveriesEmptyState');
        if (emptyState) {
            emptyState.style.display = hasVisibleRows ? 'none' : '';
        }
        
        return hasVisibleRows;
    }
}">

    <!-- Button to open Delivery History Modal -->
    <button @click="showHistoryModal = true"
           class="bg-gray-200 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2
                  hover:bg-gray-300 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Delivery History</span>
    </button>

    <!-- Delivery History Modal -->
  <div x-show="showHistoryModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

    <div @click.outside.stop="showHistoryModal = false"
         class="bg-white w-full max-w-6xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">
            <!-- Header -->
            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Delivery History</h2>
                <button @click="showHistoryModal = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

          <!-- Search + Filter Icon -->
        <div class="flex items-center gap-2 mb-4 mt-4">
            <!-- Search Input -->
            <div class="relative w-full max-w-xs">
                <input type="text"
                    x-model="searchQuery"
                    :placeholder="placeholders[placeholderIndex]"
                    @input="filterDeliveries()"
                    class="w-full pl-8 pr-10 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">

                <!-- Search Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
        </div>

            <!-- Deliveries Table -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-center">ID</th>
                            <th class="px-4 py-2 text-center">Supplier</th>
                            <th class="px-4 py-2 text-center">Requested By</th>
                            <th class="px-4 py-2 text-center">Product Type</th>
                            <th class="px-4 py-2 text-center">Received By</th>
                            <th class="px-4 py-2 text-center">Request Date</th>
                            <th class="px-4 py-2 text-center">Received Date</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                  <tbody id="deliveriesTableBody" class="divide-y divide-gray-100">
                        @php
                            $deliveredDeliveries = $deliveries->where('status', 'Delivered');
                        @endphp

                        @forelse ($deliveredDeliveries as $delivery)
                            @php
                                $types = $delivery->details->pluck('product_type')->unique()->filter();
                                if ($types->count() === 1 && $types->first()) {
                                    $productType = $types->first();
                                } elseif ($types->count() > 1) {
                                    $productType = 'Mixed';
                                } else {
                                    $productType = 'N/A';
                                }
                            @endphp

                            <tr class="group relative hover:bg-sky-200 cursor-pointer" data-delivery
                                data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '-' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $productType }} {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }} {{ $delivery->delivery_date_request }} {{ $delivery->delivery_date_received ?? '- -' }} {{ $delivery->status }}">
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
                                    @if($types->count() === 1 && $types->first())
                                        <span class="text-base font-medium">
                                            {{ $types->first() }}
                                        </span>
                                    @elseif($types->count() > 1)
                                        <span class="text-base font-medium">
                                            Mixed
                                        </span>
                                    @else
                                        <span class="text-base text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->delivery_date_request }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->delivery_date_received ?? '- -' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <span class="text-gray-800 text-xs font-semibold">{{ $delivery->status }}</span>
                                    </div>
                                </td>

                                <!-- Details Button -->
                                <td colspan="7" class="absolute inset-0 flex items-center justify-center opacity-0 
                                    group-hover:opacity-100 transition-opacity duration-200 bg-sky-100">
                                    <button type="button"
                                            class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                            @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails2 = true; showHistoryModal = false">
                                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Details
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <!-- No Delivered deliveries in database -->
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                            <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No delivered records found</p>
                                        <p class="text-sm text-gray-400 mt-1">Delivered history will appear here once available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- JS Empty State (when search/filter hides all rows) -->
                        <tr id="deliveriesEmptyState" style="display: none;">
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                        <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500"
                                    x-text="searchQuery ? 'No deliveries match your filter' : 'No deliveries found'"></p>
                                    <p class="text-sm text-gray-400 mt-1" x-show="searchQuery">
                                        Try adjusting your search or filter criteria
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Self-contained Delivery Details Modal -->
    <div x-show="showDetails2" x-cloak x-transition
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
                <button @click="showDetails2 = false; showHistoryModal = true"
                        class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>