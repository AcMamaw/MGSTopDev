<div x-data="{ showHistoryModal: false, showDetails2: false, selectedDeliveryId: null, searchQuery: '' }">

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
             class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Delivery History</h2>
                <button @click="showHistoryModal = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

          <!-- Search + Filter Icon -->
        <div x-data="{
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
                }
            }"
            class="flex items-center gap-2 mb-4 mt-4"
        >
            <!-- Search Input -->
            <div class="relative w-full max-w-xs"> <!-- Reduced width here -->
                <input type="text"
                    x-model="searchQuery"
                    :placeholder="placeholders[placeholderIndex]"
                    @input="searchFilter()"
                    class="w-full pl-8 pr-10 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">

                <!-- Search Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>

                <!-- Filter Icon -->
                <button type="button"
                        @click="nextPlaceholder()"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        title="Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 2 L16 2 L10 10 L10 16 L6 16 L6 10 Z"/>
                    </svg>
                </button>
            </div>
        </div>

            <!-- Deliveries Table -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2 text-center">ID</th>
                        <th class="px-4 py-2 text-center">Supplier</th>
                        <th class="px-4 py-2 text-center">Requested By</th>
                        <th class="px-4 py-2 text-center">Received By</th>
                        <th class="px-4 py-2 text-center">Request Date</th>
                        <th class="px-4 py-2 text-center">Received Date</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($deliveries->where('status', 'Delivered') as $delivery)
                        <tr class="group relative hover:bg-sky-200 cursor-pointer">
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
                                {{ $delivery->receiver->fname ?? '-' }} {{ $delivery->receiver->lname ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                {{ $delivery->delivery_date_request }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                {{ $delivery->delivery_date_received ?? '- -' }}
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
                    @endforeach
                </tbody>
            </table>
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
