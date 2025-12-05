<div x-data="{ 
    showPaymentHistory: false, 
    showDetails: false, 
    selectedPaymentId: null, 
    searchQuery: '',
    placeholderIndex: 0,
    placeholders: [
        'Search Payments',
        'Search ID',
        'Order ID',
        'Employee',
        'Payment Date',
        'Payment Method'
    ],
    nextPlaceholder() {
        this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
    },
    filterPayments() {
        const query = this.searchQuery.toLowerCase().trim();
        const rows = document.querySelectorAll('#paymentsTableBody tr[data-payment]');
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
        
        const emptyState = document.getElementById('paymentsEmptyState');
        if (emptyState) {
            emptyState.style.display = hasVisibleRows ? 'none' : '';
        }
        
        return hasVisibleRows;
    }
}">

    <!-- Button to open Payment History Modal -->
    <button @click="showPaymentHistory = true"
            class="bg-gray-200 text-black px-6 py-2 rounded-xl font-semibold flex items-center justify-center space-x-2
                   hover:bg-gray-300 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Payment History</span>
    </button>

    <!-- Payment History Modal -->
    <div x-show="showPaymentHistory" x-cloak x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

        <div @click.outside="showPaymentHistory = false"
            class="bg-white w-full max-w-[95vw] rounded-xl shadow-2xl p-8 relative max-h-[90vh] flex flex-col">
            
            <!-- Header -->
            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Payment History</h2>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-2 mb-4 mt-4 flex-shrink-0">
                <div class="relative w-full max-w-xs">
                    <input type="text"
                           x-model="searchQuery"
                           @input="filterPayments()"
                           :placeholder="placeholders[placeholderIndex]"
                           class="w-full pl-8 pr-10 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

            @php
                $payments = \App\Models\Payment::with(['order', 'employee'])->get();
                // Reverse the order - latest payments first
                $fullyPaidPayments = $payments->where('status', 'Fully Paid')->sortByDesc('payment_id');
            @endphp

            <!-- Table area (scrollable) -->
            <div class="overflow-x-auto overflow-y-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-center">Payment ID</th>
                            <th class="px-4 py-2 text-center">Order ID</th>
                            <th class="px-4 py-2 text-center">Customer</th>
                            <th class="px-4 py-2 text-center">Issued by</th>
                            <th class="px-4 py-2 text-center">Amount</th>
                            <th class="px-4 py-2 text-center">Cash</th>
                            <th class="px-4 py-2 text-center">Change Amount</th>
                            <th class="px-4 py-2 text-center">Date</th>
                            <th class="px-4 py-2 text-center">Method</th>
                            <th class="px-4 py-2 text-center">Reference No</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody" class="divide-y divide-gray-100">
                        @forelse ($fullyPaidPayments as $payment)
                            <tr data-payment
                                data-search="P{{ str_pad($payment->payment_id, 3, '0', STR_PAD_LEFT) }} O{{ str_pad($payment->order->order_id ?? 0, 3, '0', STR_PAD_LEFT) }} {{ $payment->order->customer->fname ?? '' }} {{ $payment->order->customer->lname ?? '' }} {{ $payment->employee->fname ?? '' }} {{ $payment->employee->lname ?? '' }} {{ number_format($payment->amount, 2) }} {{ number_format($payment->cash ?? 0, 2) }} {{ number_format($payment->change_amount ?? 0, 2) }} {{ $payment->payment_date }} {{ $payment->payment_method ?? '-' }} {{ $payment->reference_number ?? '-' }} {{ $payment->status ?? '-' }}">
                                <td class="px-4 py-3 text-center text-gray-800">
                                    P{{ str_pad($payment->payment_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    O{{ str_pad($payment->order->order_id ?? 0, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->order->customer->fname ?? '' }} {{ $payment->order->customer->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->employee->fname ?? '' }} {{ $payment->employee->lname ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->cash ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->change_amount ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->payment_date }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->payment_method ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->reference_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->status ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                            <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No fully paid payments found</p>
                                        <p class="text-sm text-gray-400 mt-1">
                                            Fully paid payments will appear here once available
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="paymentsEmptyState" style="display: none;">
                            <td colspan="11" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                        <path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500"
                                    x-text="searchQuery ? 'No payments match your filter' : 'No payments found'"></p>
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
                <button @click="showPaymentHistory = false"
                        class="bg-yellow-500 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
