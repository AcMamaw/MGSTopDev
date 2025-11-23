<div x-data="{ showPaymentHistory: false, showDetails: false, selectedPaymentId: null, searchQuery: '' }">

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

        <div @click.outside.stop="showPaymentHistory = false"
             class="bg-white w-full max-w-6xl rounded-xl shadow-2xl p-8 relative max-h-[90vh] overflow-y-auto flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center p-2 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Payment History</h2>
                <button @click="showPaymentHistory = false" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>

            <!-- Search + Filter Icon -->
            <div x-data="{
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
                    }
                }"
                class="flex items-center gap-2 mb-4 mt-4"
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

            <!-- Payments Table -->
            @php
                $payments = \App\Models\Payment::with(['order', 'employee'])->get();
            @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-center">Payment ID</th>
                            <th class="px-4 py-2 text-center">Order ID</th>
                            <th class="px-4 py-2 text-center">Issued by</th>
                            <th class="px-4 py-2 text-center">Payment Date</th>
                            <th class="px-4 py-2 text-center">Amount</th>
                            <th class="px-4 py-2 text-center">Cash</th>
                            <th class="px-4 py-2 text-center">Balance</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center">Change Amount</th>
                            <th class="px-4 py-2 text-center">Payment Method</th>
                            <th class="px-4 py-2 text-center">Reference No</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($payments->where('status', 'Fully Paid') as $payment)
                            <tr>
                                <!-- Payment ID -->
                                <td class="px-4 py-3 text-center text-gray-800">
                                    P{{ str_pad($payment->payment_id, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <!-- Order ID -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    O{{ str_pad($payment->order->order_id ?? 0, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <!-- Employee -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->employee->fname ?? '' }} {{ $payment->employee->lname ?? '' }}
                                </td>

                                <!-- Payment Date -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->payment_date }}
                                </td>

                                <!-- Amount -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>

                                <!-- Cash -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->cash ?? 0, 2) }}
                                </td>

                                <!-- Balance -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->balance ?? 0, 2) }}
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->status ?? '-' }}
                                </td>

                                <!-- Change Amount -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    ₱{{ number_format($payment->change_amount ?? 0, 2) }}
                                </td>

                                <!-- Payment Method -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->payment_method ?? '-' }}
                                </td>

                                <!-- Reference Number -->
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $payment->reference_number ?? '-' }}
                                </td>
                            </tr>
                        @endforeach

                        @if($payments->where('status', 'Fully Paid')->isEmpty())
                            <tr>
                                <td colspan="11" class="text-center py-4 text-gray-500">No fully paid payments found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
