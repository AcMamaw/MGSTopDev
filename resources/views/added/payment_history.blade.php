<div
    x-data="{
        showPaymentHistory: false,
        showReceipt: false,
        showSuccess: false,
        selectedPaymentId: null,
        searchQuery: '',
        placeholderIndex: 0,
        placeholders: [
            'Search Payments',
            'Search ID',
            'Order ID',
            'Customer',
            'Employee',
            'Payment Date',
            'Payment Method'
        ],
        receipt: {
            receipt_number: null,
            payment_date: '',
            customer_name: '',
            status: '',
            payment_method: '',
            reference_number: '',
            amount: 0,
            balance: 0,
            cash: 0,
            change_amount: 0,
            items: []
        },
        paymentMethod: '',
        paymentReference: '',
        paymentBalance: 0,
        paymentCash: 0,
        paymentChange: 0,

        init() {
            setInterval(() => this.nextPlaceholder(), 2000);
        },
        nextPlaceholder() {
            this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
        },

        filterPayments() {
            const query = this.searchQuery.toLowerCase().trim();
            const rows = document.querySelectorAll('#paymentsTableBody tr[data-payment]');
            let hasVisibleRows = false;

            rows.forEach(row => {
                if (!row.hasAttribute('data-payment')) return;
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
            const initialEmptyState = document.getElementById('paymentsInitialEmptyState');

            if (query) {
                if (emptyState) emptyState.style.display = hasVisibleRows ? 'none' : '';
                if (initialEmptyState) initialEmptyState.style.display = 'none';
            } else {
                if (emptyState) emptyState.style.display = 'none';
                if (initialEmptyState) initialEmptyState.style.display = hasVisibleRows ? 'none' : '';
            }

            return hasVisibleRows;
        },

        openReceipt(payment) {
            this.receipt.receipt_number   = payment.payment_id;
            this.receipt.payment_date     = payment.payment_date;
            this.receipt.customer_name    = payment.customer_name;
            this.receipt.status           = payment.status;
            this.receipt.payment_method   = payment.payment_method || '';
            this.receipt.reference_number = payment.reference_number || '';
            this.receipt.amount           = payment.amount || 0;
            this.receipt.balance          = payment.balance || 0;
            this.receipt.cash             = payment.cash || 0;
            this.receipt.change_amount    = payment.change_amount || 0;
            this.receipt.items            = payment.items || [];    // <<< IMPORTANT

            this.paymentMethod   = this.receipt.payment_method;
            this.paymentReference= this.receipt.reference_number;
            this.paymentBalance  = this.receipt.balance;
            this.paymentCash     = this.receipt.cash;
            this.paymentChange   = this.receipt.change_amount;

            this.showPaymentHistory = false;
            this.showReceipt = true;
        },

        printReceipt() {
            window.print();
        },

        grandTotal() {
            return this.receipt.amount || 0;
        },
        colorNameFromHex(hex) {
            return hex || '';
        }
    }"
>
    {{-- OPEN PAYMENT HISTORY BUTTON --}}
    <button
        @click="showPaymentHistory = true"
        class="bg-gray-200 text-black px-6 py-2 rounded-full font-semibold flex items-center justify-center space-x-2
            hover:bg-gray-300 transition shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
        </svg>
        <span>Payment History</span>
    </button>

    {{-- PAYMENT HISTORY MODAL --}}
    <div
        x-show="showPaymentHistory"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
    >
        <div
            @click.outside.stop="showPaymentHistory = false"
            class="bg-white w-full max-w-[95vw] lg:max-w-6xl rounded-xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto flex flex-col"
        >
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Payment History
                </h2>
            </div>

            {{-- SEARCH INSIDE MODAL --}}
            <div class="flex items-center gap-2 mb-4 mt-2 flex-shrink-0">
                <div class="relative w-full max-w-sm">
                    <input
                        type="text"
                        x-model="searchQuery"
                        :placeholder="placeholders[placeholderIndex]"
                        @input="filterPayments()"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition shadow-sm"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="16" height="16"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
            </div>

            @php
                $payments = \App\Models\Payment::with(['order.items.stock.product', 'order.customer', 'employee'])->get();
                $fullyPaidPayments = $payments->where('status', 'Fully Paid')->sortByDesc('payment_id');
            @endphp

          <div class="overflow-x-auto overflow-y-auto flex-grow rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Payment ID</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Issued by</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Cash</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Change</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Method</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody" class="divide-y divide-gray-100 bg-white">
                        @forelse ($fullyPaidPayments as $payment)
                            @php
                                $customerName = trim(($payment->order->customer->fname ?? '') . ' ' . ($payment->order->customer->lname ?? ''));
                                $employeeName = trim(($payment->employee->fname ?? '') . ' ' . ($payment->employee->lname ?? ''));
                                $itemsArray = ($payment->order->items ?? collect())->map(function($item) {
                                    return [
                                        'quantity'      => $item->quantity,
                                        'product_name'  => $item->stock->product->product_name ?? 'Product N/A',
                                        'size'          => $item->size,
                                        'color'         => $item->color,
                                        'unit_price'    => $item->price,
                                        'custom_amount' => $item->custom_amount ?? 0,
                                        'amount'        => $item->quantity * $item->price + ($item->custom_amount ?? 0),
                                    ];
                                })->values();
                            @endphp
                            <tr
                                class="cursor-pointer transition duration-150 ease-in-out hover:bg-sky-50"
                                data-payment
                                data-search="P{{ str_pad($payment->payment_id, 3, '0', STR_PAD_LEFT) }} O{{ str_pad($payment->order->order_id ?? 0, 3, '0', STR_PAD_LEFT) }} {{ $customerName }} {{ $employeeName }} {{ number_format($payment->amount, 2) }} {{ number_format($payment->cash ?? 0, 2) }} {{ number_format($payment->change_amount ?? 0, 2) }} {{ $payment->payment_date }} {{ $payment->payment_method ?? '-' }} {{ $payment->status ?? '-' }}"
                            >
                                {{-- visible cells --}}
                                <td class="px-4 py-3 text-center font-bold text-yellow-700">
                                    P{{ str_pad($payment->payment_id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    O{{ str_pad($payment->order->order_id ?? 0, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-800">
                                    {{ $customerName }}
                                </td>
                                <td class="px-4 py-3 text-left text-sm text-gray-600">
                                    {{ $employeeName }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700 font-semibold">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">
                                    ₱{{ number_format($payment->cash ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">
                                    ₱{{ number_format($payment->change_amount ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $payment->payment_date }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $payment->payment_method ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-1">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                        <span class="text-black-600 text-xs font-bold">{{ $payment->status ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- Action button: View Receipt --}}
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        @click="openReceipt({
                                            payment_id: {{ $payment->payment_id }},
                                            payment_date: '{{ $payment->payment_date }}',
                                            customer_name: @js($customerName),
                                            status: @js($payment->status ?? ''),
                                            payment_method: @js($payment->payment_method ?? ''),
                                            reference_number: @js($payment->reference_number ?? ''),
                                            amount: {{ $payment->amount ?? 0 }},
                                            balance: {{ $payment->balance ?? 0 }},
                                            cash: {{ $payment->cash ?? 0 }},
                                            change_amount: {{ $payment->change_amount ?? 0 }},
                                            items: @js($itemsArray)
                                        })"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 shadow-sm transition"
                                        title="View receipt"
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
                            <tr id="paymentsInitialEmptyState" x-show="!searchQuery && {{ $fullyPaidPayments->isEmpty() ? 'true' : 'false' }}">
                                <td colspan="11" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a10 10 0 0 0 0 20c3.5 0 6.6-1.7 8.5-4.3M12 18v-6l-3-3m3 3h5m0 0a9 9 0 0 0-4.5-8.6"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No fully paid records found</p>
                                        <p class="text-sm text-gray-400 mt-1">Fully paid history will appear here once available</p>
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


            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button
                    @click="showPaymentHistory = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>


    <!-- PRINT STYLES -->
  <style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableReceipt, #printableReceipt * {
            visibility: visible;
        }
        #printableReceipt {
            position: relative;
            visibility: visible;
            margin: 0 auto !important;
            top: 0;
            left: 0;
            right: 0;
            width: 80%;
            min-height: auto;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: #ffffff !important;
        }
        html, body {
            height: auto;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
    }
    </style>

    <!-- Success toast -->
    <div
        x-show="showSuccess"
        x-cloak
        x-transition
        class="no-print fixed top-4 left-1/2 -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm z-[9999]">
        Your order was processed successfully.
    </div>

    <!-- RECEIPT MODAL -->
  <div x-show="showReceipt" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div id="printableReceipt"
             @click.away="showReceipt = false; showPaymentHistory = true"
             class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative text-gray-800 max-h-[90vh] overflow-y-auto">

            <!-- Print FAB -->
            <button type="button"
                    @click="printReceipt()"
                    class="no-print fixed bottom-8 right-8 p-4 rounded-full bg-yellow-400 text-black shadow-2xl hover:bg-yellow-500 hover:scale-110 transition-all duration-200 z-50 border-4 border-white"
                    title="Print Receipt">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
            </button>

                <div class="grid grid-cols-3 gap-4 items-start mb-4">
            <div class="col-span-2">
                <h1 class="text-lg font-bold tracking-wide">Mariviles Graphic Studio</h1>
                <p class="text-xs">Adopted CO.</p>
                <p class="text-xs">Mati City</p>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="flex items-center justify-center w-40 h-20 rounded-md mb-1">
                    <img
                        src="{{ asset('images/ace.jpg') }}"
                        alt="Company Logo"
                        class="object-contain max-w-full max-h-full">
                </div>
            </div>
        </div>

        <!-- Receipt # + date -->
        <div class="flex justify-between items-start mb-3">
            <div class="text-left text-[11px] space-y-0.5">
                <p>
                    <span class="font-semibold">Receipt #:</span>
                    <span x-text="receipt.receipt_number ? ('R-' + String(receipt.receipt_number).padStart(5,'0')) : 'N/A'"></span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-xl tracking-[0.35em] font-semibold text-yellow-400">RECEIPT</p>
                <div class="mt-3 text-[11px] space-y-0.5">
                    <p>
                        <span class="font-semibold">Receipt date:</span>
                        <span x-text="receipt.payment_date || 'N/A'"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Billed to + status -->
        <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
            <div>
                <p class="uppercase text-[10px] font-semibold text-gray-500">Billed To</p>
                <p class="font-semibold" x-text="receipt.customer_name"></p>
            </div>
            <div class="text-right space-y-0.5">
                <p>
                    <span class="font-semibold">Status:</span>
                    <span x-text="receipt.status"></span>
                </p>
                <p>
                    <span class="font-semibold">Payment Method:</span>
                    <span x-text="receipt.payment_method || paymentMethod"></span>
                </p>
                <template x-if="(receipt.payment_method || paymentMethod) === 'GCash'">
                    <p>
                        <span class="font-semibold">Reference #:</span>
                        <span x-text="receipt.reference_number || paymentReference"></span>
                    </p>
                </template>
            </div>
        </div>

        <!-- Table header (Qty / Item / Unit Price / Customize / Total) -->
        <div class="mt-3 border-t border-b border-yellow-400 bg-yellow-400 text-[11px] font-semibold">
            <div class="grid grid-cols-12 py-1.5 px-3">
                <div class="col-span-1 text-left text-black">Qty</div>
                <div class="col-span-5 text-left text-black">Item</div>
                <div class="col-span-3 text-right text-black">Unit Price</div>
                <div class="col-span-3 text-right text-black">Total</div>
            </div>
        </div>

        <!-- Line items -->
        <template x-for="(item, index) in receipt.items" :key="index">
            <div class="grid grid-cols-12 py-1.5 px-3 text-[11px] border-b border-gray-100">
                <div class="col-span-1 font-bold text-base" x-text="item.quantity || item.qty || '1'"></div>

                <div class="col-span-5">
                    <p class="font-semibold text-gray-800" x-text="item.product_name || item.description || 'N/A'"></p>                  
                    <p class="text-[10px] text-gray-600" x-show="item.size || item.color">
                        <span x-show="item.size">Size: <span x-text="item.size"></span></span>
                        <span x-show="item.color">
                            | Color:
                            <span x-text="colorNameFromHex(item.color)"></span>
                        </span>
                    </p>
                </div>

                <!-- Unit price (selling with markup) -->
                <div class="col-span-3 text-right">
                    ₱<span x-text="Number(item.unit_price || 0).toFixed(2)"></span>
                </div>

                <!-- Line total -->
                <div class="col-span-3 text-right font-semibold">
                    ₱<span x-text="Number(item.amount || 0).toFixed(2)"></span>
                </div>
            </div>
        </template>

        <!-- Totals -->
        <div class="mt-3 flex justify-end">
            <div class="w-56 text-[11px] space-y-0.5">
                <!-- Subtotal / Total stay the same -->
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>
                        ₱<span x-text="Number(receipt.amount || grandTotal()).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between font-semibold border-t border-gray-200 pt-1">
                    <span>Total</span>
                    <span>
                        ₱<span x-text="Number(receipt.amount || grandTotal()).toFixed(2)"></span>
                    </span>
                </div>

                 <!-- Balance FIRST, only red if > 0 -->
                <div class="flex justify-between pt-1">
                    <span>Balance</span>
                    <span
                        :class="Number(receipt.balance || paymentBalance || 0) > 0
                                ? 'text-red-600 font-semibold'
                                : 'text-black font-semibold'">
                        ₱<span x-text="Number(receipt.balance || paymentBalance || 0).toFixed(2)"></span>
                    </span>
                </div>

                <!-- Cash -->
                <div class="flex justify-between">
                    <span>Cash</span>
                    <span>
                        ₱<span x-text="Number(receipt.cash || paymentCash || 0).toFixed(2)"></span>
                    </span>
                </div>

                <!-- Change -->
                <div class="flex justify-between">
                    <span>Change</span>
                    <span>
                        ₱<span x-text="Number(receipt.change_amount || paymentChange || 0).toFixed(2)"></span>
                    </span>
                </div>
            </div>
        </div>


        <div class="mt-4 text-[11px]">
            <p class="font-semibold mb-1">Notes</p>
            <p>
                Thank you for choosing Mariviles Graphic Studio.
                Your positive feedback helps us continue providing quality service.
            </p>
        </div>

        <div class="mt-4 flex justify-end">
            <div class="text-[11px] text-right">
                <p class="font-semibold border-b border-gray-400 inline-block px-6 pb-1">
                    {{ auth()->user()->employee->fname ?? '' }}
                    {{ auth()->user()->employee->lname ?? '' }}
                </p>
                <p class="mt-1 text-center text-gray-600">Authorized by</p>
            </div>
        </div>

            <!-- Close button -->
            <div class="mt-6 flex justify-center no-print">
                <button type="button"
                        @click="showReceipt = false; showPaymentHistory = true"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>