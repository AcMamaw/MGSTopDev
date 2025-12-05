<div x-data="{ 
    showOrderHistory: false, 
    showOrderDetails2: false, 
    selectedOrderId: null,
    searchQuery: '',
    placeholderIndex: 0,
    placeholders: [
        'Search Orders',
        'Search ID',
        'Customer',
        'Order Date',
        'Status'
    ],
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterOrders() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#ordersTableBody tr[data-order]');
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

        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = hasVisibleRows ? 'none' : '';
        }

        return hasVisibleRows;
    }
}">

    <!-- Button to open Order History -->
    <button @click="showOrderHistory = true"
        class="bg-gray-200 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2 hover:bg-gray-300 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Order History</span>
    </button>

    <!-- Order History Modal -->
   <div x-show="showOrderHistory" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div @click.outside.stop="showOrderHistory = false"
         class="bg-white w-full max-w-[95vw] rounded-xl shadow-2xl p-8 relative max-h-[90vh] flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
            </div>

            <!-- Orders Search -->
            <div class="flex items-center gap-2 mb-2 mt-2 flex-shrink-0">
                <div class="relative w-full max-w-xs">
                    <input type="text"
                           x-model="searchQuery"
                           @input="filterOrders()"
                           :placeholder="placeholders[placeholderIndex]"
                           class="w-full pl-8 pr-10 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

            <!-- Orders Table (scrollable area) -->
            <div class="overflow-x-auto overflow-y-auto mt-4 flex-1">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product type</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Issued by</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Order date</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="divide-y divide-gray-100">
                        @php
                            $completedOrders = isset($allOrders) 
                                ? $allOrders->where('status','Completed') 
                                : collect([]);
                        @endphp

                        @forelse ($completedOrders as $order)
                            <tr class="group relative hover:bg-green-100 cursor-pointer"
                                data-order
                                data-search="O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }} {{ $order->category->category_name ?? 'N/A' }} {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }} {{ $order->product_type ?? 'N/A' }} {{ $order->employee ? $order->employee->fname . ' ' . $order->employee->lname : '-' }} {{ $order->order_date }} {{ $order->status }}">
                                <td class="px-4 py-3 text-center text-gray-800 group-hover:opacity-0">
                                    O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->category->category_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->product_type ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->employee ? $order->employee->fname . ' ' . $order->employee->lname : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center group-hover:opacity-0">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                                    </div>
                                </td>

                                <!-- Hover Details Button -->
                                <td colspan="8" class="absolute inset-0 flex items-center justify-center opacity-0 
                                    group-hover:opacity-100 transition-opacity duration-200 bg-green-100">
                                    <button type="button"
                                            class="w-full h-full flex items-center justify-center bg-sky-200 hover:bg-sky-300 transition-colors"
                                            @click="selectedOrderId = {{ $order->order_id }}; showOrderDetails2 = true; showOrderHistory = false">
                                        <span class="text-sky-700 font-semibold text-sm hover:font-bold transition-all duration-200">
                                            Details
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                            <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No completed orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">Completed orders will appear here once available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- JS Empty State -->
                        <tr id="emptyState" style="display: none;">
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                        <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500"
                                       x-text="searchQuery ? 'No orders match your filter' : 'No orders found'"></p>
                                    <p class="text-sm text-gray-400 mt-1" x-show="searchQuery">
                                        Try adjusting your search or filter criteria
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer with Close -->
            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button @click="showOrderHistory = false"
                        class="bg-yellow-500 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div x-show="showOrderDetails2" x-cloak x-transition
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    Order Details - ID: <span x-text="'O' + String(selectedOrderId).padStart(3, '0')"></span>
                </h2>
            </div>

            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Item ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Color</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($allOrders))
                        @foreach ($allOrders as $order)
                            @php $grandTotal = $order->items->sum(fn($item)=> $item->quantity * $item->price); @endphp
                            @foreach ($order->items as $item)
                                <tr x-show="selectedOrderId === {{ $order->order_id }}">
                                    <td class="px-4 py-2 text-center">OD{{ str_pad($item->orderdetails_id,3,'0',STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->stock->product->product_name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->size ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <span class="w-4 h-4 rounded-full border" 
                                                  style="background-color: {{ $item->color ?? '#ffffff' }};">
                                            </span>
                                            <span>{{ $item->color ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->stock->product->unit ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right">₱{{ number_format($item->price,2) }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity * $item->price,2) }}</td>
                                </tr>
                            @endforeach
                            <tr x-show="selectedOrderId === {{ $order->order_id }}">
                                <td colspan="7"
                                    class="px-4 py-3 text-right font-bold text-gray-700">
                                    GRAND TOTAL:
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                    ₱{{ number_format($grandTotal,2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div class="mt-6 flex justify-end">
                <button @click="showOrderDetails2 = false; showOrderHistory = true"
                        class="bg-yellow-500 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>