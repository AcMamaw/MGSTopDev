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

            <!-- Cash Amount Received -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Cash Amount Received</label>
                <input type="number" x-model.number="paymentCash" step="0.01" min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
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

            <!-- Payment Summary -->
            <div x-show="paymentCash > 0" 
                 class="bg-blue-50 border border-blue-300 rounded-lg p-4 mt-2">
                <h3 class="font-semibold text-blue-900 mb-2">Payment Summary:</h3>
                <div class="space-y-1 text-sm text-blue-800">
                    <p>Current Balance: ₱<span x-text="paymentBalance.toFixed(2)"></span></p>
                    <p>Cash Received: ₱<span x-text="paymentCash.toFixed(2)"></span></p>
                    <hr class="my-2 border-blue-200">
                    <p class="font-semibold">New Balance: ₱<span x-text="Math.max(paymentBalance - paymentCash, 0).toFixed(2)"></span></p>
                    <p class="font-semibold">Change: ₱<span x-text="Math.max(paymentCash - paymentBalance, 0).toFixed(2)"></span></p>
                    <p class="font-semibold">Status: <span x-text="paymentCash >= paymentBalance ? 'Fully Paid' : 'Partial'"></span></p>
                </div>
            </div>

            <!-- Dynamic Warning -->
            <div x-show="paymentCash > 0" 
                 :class="paymentCash < paymentBalance ? 'bg-yellow-50 border border-yellow-300 text-yellow-800' : 'bg-green-50 border border-green-300 text-green-800'"
                 class="rounded-lg p-3 mt-2">
                <template x-if="paymentCash < paymentBalance">
                    <p class="font-semibold">⚠️ Partial Payment - Remaining balance: ₱<span x-text="(paymentBalance - paymentCash).toFixed(2)"></span></p>
                </template>
                <template x-if="paymentCash >= paymentBalance">
                    <p class="font-semibold">✅ Full Payment - Change to return: ₱<span x-text="(paymentCash - paymentBalance).toFixed(2)"></span></p>
                </template>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showCompletePaymentModal = false"
                     class="px-6 py-2 rounded-lg border border-yellow-400 text-black font-semibold bg-transparent hover:bg-yellow-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>