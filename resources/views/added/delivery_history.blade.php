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
    // Function to cycle through placeholders every 2 seconds (optional visual effect)
    init() {
        setInterval(() => {
            this.nextPlaceholder();
        }, 2000); // Change placeholder every 2 seconds
    },
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterDeliveries() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#deliveriesTableBody tr[data-delivery]');
        let hasVisibleRows = false;

        rows.forEach(row => {
            if (row.hasAttribute('data-delivery')) {
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

    <button @click="showHistoryModal = true"
            class="bg-gray-200 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center space-x-2
                hover:bg-gray-300 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Delivery History</span>
    </button>

    <div x-show="showHistoryModal" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

        <div @click.outside.stop="showHistoryModal = false"
             class="bg-white w-full max-w-[95vw] lg:max-w-6xl rounded-xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Delivery History
                </h2>
            </div>

            <div class="flex items-center gap-2 mb-4 mt-2">
                <div class="relative w-full max-w-sm">
                    <input type="text"
                        x-model="searchQuery"
                        :placeholder="placeholders[placeholderIndex]"
                        @input="filterDeliveries()"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition shadow-sm">

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-auto flex-grow rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Requested by</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Product type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Received by</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Requested</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Received</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="deliveriesTableBody" class="divide-y divide-gray-100 bg-white">
                        @php
                    
                            $deliveredDeliveries = isset($deliveries) ? $deliveries->where('status', 'Delivered')->sortByDesc('delivery_id') : collect([]);
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

                            <tr class="group relative hover:bg-sky-50 transition duration-150 ease-in-out cursor-pointer" data-delivery
                                data-search="D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }} {{ $delivery->supplier->supplier_name ?? '-' }} {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }} {{ $productType }} {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }} {{ $delivery->delivery_date_request }} {{ $delivery->delivery_date_received ?? '- -' }} {{ $delivery->status }}">
                                
                                <td class="px-4 py-3 text-center font-bold text-yellow-700 group-hover:opacity-0">
                                    D{{ str_pad($delivery->delivery_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-800 group-hover:opacity-0">
                                    {{ $delivery->supplier->supplier_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->employee->fname ?? '' }} {{ $delivery->employee->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600 group-hover:opacity-0">
                                    <span>
                                        {{ $productType }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->delivery_date_request }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600 group-hover:opacity-0">
                                    {{ $delivery->delivery_date_received ?? '- -' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-1 group-hover:opacity-0">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                        <span class="text-black-600 text-xs font-bold">{{ $delivery->status }}</span>
                                    </div>
                                </td>

                                <td colspan="8" class="absolute inset-0 flex items-center justify-center opacity-0 z-20
                                    group-hover:opacity-100 transition-opacity duration-200 bg-sky-100/80 backdrop-blur-sm">
                                    <button type="button"
                                            class="flex-1 flex items-center justify-center bg-sky-100 hover:bg-sky-200 transition-colors py-3"
                                            @click="selectedDeliveryId = {{ $delivery->delivery_id }}; showDetails2 = true; showHistoryModal = false">
                                        <span class="text-sky-800 font-extrabold text-sm uppercase tracking-wider">
                                            View Details
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="deliveriesInitialEmptyState" x-show="!searchQuery && {{ $deliveredDeliveries->isEmpty() ? 'true' : 'false' }}">
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0 0 20c3.5 0 6.6-1.7 8.5-4.3M12 18v-6l-3-3m3 3h5m0 0a9 9 0 0 0-4.5-8.6"/></svg>
                                        <p class="text-lg font-medium text-gray-500">No delivered records found</p>
                                        <p class="text-sm text-gray-400 mt-1">Delivered history will appear here once available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

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

            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button @click="showHistoryModal = false"
                    class="bg-yellow-500 text-gray-900 font-bold px-8 py-2 rounded-full hover:bg-yellow-600 transition shadow-md">
                    Close
                </button>
            </div>
        </div>
    </div>


    <div x-show="showDetails2" x-transition x-cloak
        class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showDetails = false"
            class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">
                 Delivery Details - ID: <span class="text-black-600" x-text="'D' + selectedDeliveryId.toString().padStart(3, '0')"></span>
            </h2>

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
                                <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-center text-sm font-semibold text-yellow-700">
                                        DD{{ str_pad($item->deliverydetails_id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4 py-2 text-left text-sm text-gray-800">{{ $item->product->product_name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700 font-medium">{{ $item->quantity_product }}</td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $item->unit ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-700">₱{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-4 py-2 text-right text-sm font-semibold text-gray-900">₱{{ number_format($item->quantity_product * $item->unit_cost, 2) }}</td>
                                </tr>
                            @endforeach

                            <tr x-show="selectedDeliveryId === {{ $delivery->delivery_id }}" class="bg-black-50/50">
                                <td colspan="5" class="px-4 py-3 text-right font-bold text-base text-gray-700">GRAND TOTAL:</td>
                                <td class="px-4 py-3 text-right font-semibold text-base text-black-800">₱{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="showDetails2 = false; showHistoryModal =true"
                        class="bg-yellow-500 text-gray-900 font-bold px-8 py-2 rounded-full hover:bg-yellow-600 transition shadow-md">
                    Close
                </button>
            </div>
        </div>
    </div>


</div>