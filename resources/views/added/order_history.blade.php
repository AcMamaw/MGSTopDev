<div x-data="{ showOrderHistory: false, showOrderDetails2: false, selectedOrderId: null }">

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
    <div x-show="showOrderHistory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside.stop="showOrderHistory = false" class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
                <button @click="showOrderHistory = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

            <!-- Orders Search + Filter Icon -->
<div x-data="{
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
        }
    }"
    class="flex items-center gap-2 mb-2 mt-2"
>
    <div class="relative w-full max-w-xs">
        <input type="text"
               x-model="searchQuery"
               :placeholder="placeholders[placeholderIndex]"
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

            
          <!-- Orders Table -->
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2 text-center">Order ID</th>
                        <th class="px-4 py-2 text-center">Customer</th>
                        <th class="px-4 py-2 text-center">Issued by</th>
                        <th class="px-4 py-2 text-center">Order Date</th>
                        <th class="px-4 py-2 text-center">Total Amount</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        @if($order->status === 'Completed')
                            <tr class="group relative hover:bg-green-100 cursor-pointer">
                                <td class="px-4 py-3 text-center text-gray-800 group-hover:opacity-0">
                                    O{{ str_pad($order->order_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->customer->fname ?? '' }} {{ $order->customer->lname ?? '' }}
                                </td>
                               <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->employee ? $order->employee->fname . ' ' . $order->employee->lname : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    {{ $order->order_date }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 group-hover:opacity-0">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center group-hover:opacity-0 flex justify-center items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                    <span class="text-gray-800 text-xs font-semibold">{{ $order->status }}</span>
                                </td>

                                <!-- Details Button -->
                                <td colspan="7" class="absolute inset-0 flex items-center justify-center opacity-0 
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
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        </div>
    </div>

    <!-- Order Details Modal -->
    <div x-show="showOrderDetails2" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    Order Details - ID: <span x-text="selectedOrderId"></span>
                </h2>
            </div>

            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Item ID</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        @php $grandTotal = $order->items->sum(fn($item)=> $item->quantity * $item->price); @endphp
                        @foreach ($order->items as $item)
                            <tr x-show="selectedOrderId === {{ $order->order_id }}">
                                <td class="px-4 py-2 text-center">OD{{ str_pad($item->orderdetails_id,3,'0',STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->stock->product->product_name ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->stock->product->unit ?? '-' }}</td>
                                <td class="px-4 py-2 text-right">₱{{ number_format($item->price,2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold">₱{{ number_format($item->quantity * $item->price,2) }}</td>
                            </tr>
                        @endforeach
                        <tr x-show="selectedOrderId === {{ $order->order_id }}">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">GRAND TOTAL:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₱{{ number_format($grandTotal,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6 flex justify-end">
                <button @click="showOrderDetails2 = false; showOrderHistory = true"
                        class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>
