<!-- COMPLETE PAYMENT MODAL -->
<div x-show="showCompletePaymentModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div @click.outside="showCompletePaymentModal = false"
         class="bg-white w-full max-w-3xl rounded-xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Complete Payment</h2>
        </div>

        <form @submit.prevent="submitCompletePayment()" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" :value="selectedOrderId">

            <!-- Current Balance -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Current Balance</label>
                <input type="number" :value="paymentBalance" readonly
                       class="w-full px-4 py-2 border rounded-lg bg-gray-100 font-bold text-lg">
            </div>

             <!-- Payment Method -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Payment Method</label>
                <select x-model="paymentMethod"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                    <option value="">Select Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                </select>
            </div>

            <!-- Reference Number (only for GCash) -->
            <div x-show="paymentMethod === 'GCash'">
                <label class="block text-gray-700 font-medium mb-1">Reference Number</label>
                <input type="text" x-model="paymentReference"
                       :required="paymentMethod === 'GCash'"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
            </div>

            <!-- Cash Amount Received -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Cash Amount Received</label>
                <input type="number" x-model.number="paymentCash" step="0.01" min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
            </div>

          
            <!-- Payment Summary -->
            <div x-show="paymentCash > 0"
                 class="bg-blue-50 border border-blue-300 rounded-lg p-4 mt-2">
                <h3 class="font-semibold text-blue-900 mb-2">Payment Summary:</h3>
                <div class="space-y-1 text-sm text-blue-800">
                    <p>Current Balance: ₱<span x-text="paymentBalance.toFixed(2)"></span></p>
                    <p>Cash Received: ₱<span x-text="paymentCash.toFixed(2)"></span></p>
                    <hr class="my-2 border-blue-200">
                    <p class="font-semibold">
                        New Balance: ₱
                        <span x-text="Math.max(paymentBalance - paymentCash, 0).toFixed(2)"></span>
                    </p>
                    <p class="font-semibold">
                        Change: ₱
                        <span x-text="Math.max(paymentCash - paymentBalance, 0).toFixed(2)"></span>
                    </p>
                    <p class="font-semibold">
                        Status:
                        <span x-text="paymentCash >= paymentBalance ? 'Fully Paid' : 'Partial'"></span>
                    </p>
                </div>
            </div>

            <!-- Dynamic Warning -->
            <div x-show="paymentCash > 0"
                 :class="paymentCash < paymentBalance ? 'bg-yellow-50 border border-yellow-300 text-yellow-800' : 'bg-green-50 border border-green-300 text-green-800'"
                 class="rounded-lg p-3 mt-2">
                <template x-if="paymentCash < paymentBalance">
                    <p class="font-semibold">
                        ⚠️ Partial Payment - Remaining balance: ₱
                        <span x-text="(paymentBalance - paymentCash).toFixed(2)"></span>
                    </p>
                </template>
                <template x-if="paymentCash >= paymentBalance">
                    <p class="font-semibold">
                        ✅ Full Payment - Change to return: ₱
                        <span x-text="(paymentCash - paymentBalance).toFixed(2)"></span>
                    </p>
                </template>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showCompletePaymentModal = false"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>

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

<div
    x-show="showSuccess"
    x-cloak
    x-transition
    class="no-print fixed top-4 left-1/2 -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm z-[9999]">
    Your order was processed successfully.
</div>


<div x-show="showReceipt" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div id="printableReceipt"
         @click.away="showReceipt = false"
         class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative text-gray-800 max-h-[90vh] overflow-y-auto">

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
                <div class="col-span-2 text-right text-black">Unit Price</div>
                <div class="col-span-2 text-right text-black">Customize</div>
                <div class="col-span-2 text-right text-black">Total</div>
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
                <div class="col-span-2 text-right">
                    ₱<span x-text="Number(item.unit_price || 0).toFixed(2)"></span>
                </div>

                <!-- Customize fee (line) -->
                <div class="col-span-2 text-right">
                    ₱<span x-text="Number(item.custom_amount || 0).toFixed(2)"></span>
                </div>

                <!-- Line total -->
                <div class="col-span-2 text-right font-semibold">
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

        <div class="mt-6 flex justify-center no-print">
            <button type="button"
                    @click="showReceipt = false; showSuccess = true; window.location.reload()"
                    class="px-8 py-2 rounded-lg bg-yellow-400 text-black text-sm font-semibold hover:bg-yellow-500 transition">
                Close
            </button>
        </div>
    </div>
</div>
