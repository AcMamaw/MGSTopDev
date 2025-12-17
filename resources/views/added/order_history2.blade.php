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
    init() {
        setInterval(() => this.nextPlaceholder(), 2000);
    },
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterOrders() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#ordersTableBody tr[data-order]');
        let hasVisibleRows = false;

        rows.forEach(row => {
            if (!row.hasAttribute('data-order')) return;
            if (!query) {
                row.style.display = '';
                hasVisibleRows = true;
            } else {
                const searchableText = (row.getAttribute('data-search') || '').toLowerCase();
                if (searchableText.includes(query)) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            }
        });

        const emptyState = document.getElementById('emptyState');
        const initialEmptyState = document.getElementById('initialEmptyState');
        const hasData = document.querySelectorAll('#ordersTableBody tr[data-order]').length > 0;

        if (query) {
            if (emptyState) emptyState.style.display = hasVisibleRows ? 'none' : '';
            if (initialEmptyState) initialEmptyState.style.display = 'none';
        } else {
            if (emptyState) emptyState.style.display = 'none';
            if (initialEmptyState) initialEmptyState.style.display = hasVisibleRows || !hasData ? 'none' : '';
        }

        return hasVisibleRows;
    }
}">

    <!-- Button to open Order History -->
    <button
        @click="showOrderHistory = true"
        class="bg-gray-200 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center space-x-2 hover:bg-gray-300 transition shadow-md"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Order History</span>
    </button>

    <!-- Order History Modal -->
    <div x-show="showOrderHistory" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside.stop="showOrderHistory = false"
             class="bg-white w-full max-w-[95vw] lg:max-w-6xl rounded-xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M16 13H8"/>
                        <path d="M16 17H8"/>
                        <path d="M10 9H8"/>
                    </svg>
                    Order History
                </h2>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center gap-2 mb-4 mt-2 flex-shrink-0">
                <div class="relative w-full max-w-sm">
                    <input type="text"
                           x-model="searchQuery"
                           @input="filterOrders()"
                           :placeholder="placeholders[placeholderIndex]"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

           <!-- Orders Table -->
            <div class="overflow-x-auto overflow-y-auto flex-grow rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Issued by</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Order date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="divide-y divide-gray-100 bg-white">
                        @php
                            $completedOrders = isset($allOrders)
                                ? $allOrders->where('status','Completed')
                                : collect([]);
                        @endphp

                        @forelse ($completedOrders as $order)
                            <tr class="cursor-pointer transition duration-150 ease-in-out hover:bg-sky-50"
                                data-order
                                data-search="O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }} {{ $order->category->category_name ?? 'N/A' }} {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }} {{ $order->product_type ?? 'N/A' }} {{ $order->employee ? $order->employee->fname . ' ' . $order->employee->lname : '-' }} {{ $order->order_date }} {{ $order->status }}">
                                <td class="px-4 py-3 text-center font-bold text-yellow-700">
                                    O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600">
                                    {{ $order->category->category_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-800">
                                    {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600">
                                    {{ $order->product_type ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600">
                                    {{ $order->employee ? $order->employee->fname . ' ' . $order->employee->lname : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700 font-semibold">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-1">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                        <span class="text-black-600 text-xs font-bold">{{ $order->status }}</span>
                                    </div>
                                </td>

                                {{-- Action: open completed-order details modal --}}
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        @click="
                                            selectedOrderId = {{ $order->order_id }};
                                            showOrderDetails2 = true;
                                            showOrderHistory = false;
                                        "
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
                            <tr id="initialEmptyState"
                                x-show="!searchQuery && {{ $completedOrders->isEmpty() ? 'true' : 'false' }}">
                                <td colspan="9" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" stroke-width="2"/>
                                            <path d="M8 10h8" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M8 14h8" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M8 18h8" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No completed orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">Completed orders will appear here once available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- JS Empty State -->
                        <tr id="emptyState" style="display: none;">
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Footer -->
            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button @click="showOrderHistory = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div x-show="showOrderDetails2" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div @click.away="showOrderDetails2 = false"
             class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">
                Order Details - ID:
                <span class="text-black-600" x-text="'O' + selectedOrderId.toString().padStart(3, '0')"></span>
            </h2>

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
                        @if(isset($allOrders))
                            @foreach ($allOrders as $order)
                                @php
                                    $grandTotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                                @endphp

                                @foreach ($order->items ?? [] as $item)
                                    <tr x-show="selectedOrderId === {{ $order->order_id }}" class="hover:bg-gray-50 transition-colors">
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
                                                      style="background-color: {{ $item->color ?? '#ffffff' }};">
                                                </span>
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
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="showOrderDetails2 = false; showOrderHistory = true"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>