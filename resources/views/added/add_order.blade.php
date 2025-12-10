<!-- Combined Add Order + Payment Modal -->
<div x-show="showAddOrder" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl relative overflow-y-auto max-h-[calc(100vh-2rem)] p-6 flex gap-6"
         x-data="orderData()">

        <!-- Left: Add Order (65%) -->
        <div class="[flex-basis:65%] border-r pr-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Order</h2>

            <!-- Customer Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2">
                    <select name="customer_id" x-model="selectedCustomer" required
                            class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}">
                                {{ $customer->fname }} {{ $customer->lname }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button"
                            @click.stop="openCustomerModal()"
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                        Add Customer
                    </button>
                </div>
            </div>


            <!-- Add Customer Modal -->
            <div x-show="showAddCustomerModal" x-cloak x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div @click.away="closeCustomerModal()"
                     class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl relative">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Customer</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">First Name</label>
                                <input type="text" x-model="fname"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                                <input type="text" x-model="mname"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                                <input type="text" x-model="lname"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Contact No</label>
                                <input type="text" x-model="contact_no"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Address</label>
                                <input type="text" x-model="address"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="closeCustomerModal()"
                        class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 font-semibold bg-white hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="button" @click="addCustomer()"
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

          <!-- Product Type -->
            <div class="flex flex-col md:flex-row items-center gap-2 mb-4">
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Type</label>
                    <select name="product_type" x-model="product_type"
                            @change="onFilterChange()"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Product Type</option>
                        <option value="stockin_id">Ready Made</option>
                        <option value="deliverydetails_id">Customized Item</option>
                    </select>
                </div>
            </div>

            <!-- Order Items (Card Layout) -->
            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="bg-white border rounded-xl shadow p-4">
                        <!-- Card Header -->
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Item <span x-text="index + 1"></span>
                            </h3>
                            <div class="flex space-x-2">
                                <!-- Duplicate Button -->
                                <button type="button"
                                        @click="duplicateItem(index)"
                                        class="text-blue-400 hover:text-blue-600 hover:bg-blue-100 p-2 rounded-full transition"
                                        title="Duplicate Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button type="button"
                                        @click="items.splice(index, 1);"
                                        class="text-red-400 hover:text-red-600 hover:bg-red-100 p-2 rounded-full transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="3" y1="6" x2="21" y2="6" />
                                        <rect x="6" y="6" width="12" height="14" rx="2" />
                                        <line x1="10" y1="10" x2="10" y2="18" />
                                        <line x1="14" y1="10" x2="14" y2="18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                     <!-- Row 1: Product, Category, Size, Color -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                            <!-- Product -->
                            <div class="md:col-span-4">
                                <label class="text-sm font-medium text-gray-700">Product</label>
                                <select :name="'items['+index+'][product_name]'"
                                        x-model="item.selected_product_name"
                                        @change="onProductSelect(item, index)"
                                        :disabled="!product_type"
                                        class="w-full mt-1 px-2 py-2 border rounded-lg bg-white disabled:bg-gray-100 text-sm">
                                    <option value="">Select Product</option>
                                    <template x-for="product in getUniqueProducts()" :key="product.name">
                                        <option :value="product.name" x-text="product.label"></option>
                                    </template>
                                </select>
                            </div>

                          <!-- Category (readonly) -->
                            <div class="md:col-span-3">
                                <label class="text-sm font-medium text-gray-700">Category</label>
                                <div class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-sm text-gray-700">
                                    <span x-text="item.category_name || '—'"></span>
                                </div>
                            </div>

                            <!-- Size -->
                            <div class="md:col-span-4">
                                <label class="text-sm font-medium text-gray-700">
                                    Size <span x-show="item.size" class="text-xs text-green-600">(Auto-filled)</span>
                                </label>
                                <select :name="'items['+index+'][size]'"
                                        x-model="item.size"
                                        @change="onSizeSelect(item)"
                                        :disabled="!item.selected_product_name"
                                        class="w-full mt-1 px-2 py-2 border rounded-lg bg-white disabled:bg-gray-100 text-sm"
                                        :class="{'bg-green-50': item.size}">
                                    <option value="">Select Size</option>
                                    <template x-for="sizeOption in getAvailableSizesForProduct(item.selected_product_name, index)" :key="sizeOption.size">
                                        <option :value="sizeOption.size"
                                                :disabled="sizeOption.disabled"
                                                :class="{'text-gray-400': sizeOption.disabled, 'font-semibold': !sizeOption.disabled}"
                                                x-text="sizeOption.size + (sizeOption.disabled ? ' (Out of stock)' : ' (Available: ' + sizeOption.stock + ')')">
                                        </option>
                                    </template>
                                </select>
                            </div>

                            <!-- Color -->
                            <div class="md:col-span-1 flex flex-col">
                                <label class="text-sm font-medium text-gray-700">Color</label>
                                <input type="color"
                                    :name="'items['+index+'][color]'"
                                    x-model="item.color"
                                    :disabled="!item.stock_id"
                                    class="w-full h-9 mt-1 border rounded cursor-pointer disabled:bg-gray-100">
                            </div>
                        </div>

                      <!-- Row 2: Quantity, Price, Customize, Total -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-4">
                            <!-- Quantity -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" min="1"
                                    x-model.number="item.quantity"
                                    @input="updateTotal(item)"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg text-right">
                            </div>

                            <!-- Price (with markup) -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Price</label>
                                <input type="text" readonly
                                    :value="formatMoney(item.price)"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-right">
                            </div>

                            <!-- Customize Amount (only for Customized Item) -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Customize Amount</label>
                                <input type="text" readonly
                                    :value="formatMoney(item.custom_amount)"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-right">
                            </div>

                            <!-- Total -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total</label>
                                <input type="text" readonly
                                    :value="formatMoney(item.total)"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-right">
                            </div>
                        </div>

                        <!-- Hidden -->
                        <input type="hidden" :name="'items['+index+'][stock_id]'" x-model="item.stock_id">
                    </div>
                </template>

                <!-- Add Item Button -->
                <div class="w-full flex justify-center mt-3">
                    <button type="button"
                            @click="addNewItem()"
                            class="px-6 py-2 rounded-full bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 transition shadow-md shadow-yellow-200/50">
                        + Add Item
                    </button>
                </div>
                <br>
            </div>

            <!-- Footer: Issued By -->
           <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">
                <div class="text-lg font-semibold">
                    Issued by: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
                </div>

                <div class="text-right space-y-1">
                    <div class="text-xl font-bold">
                        Grand Total: ₱
                        <span x-text="formatMoney(grandTotal())"></span>
                    </div>
                </div>
            </div>
            <br>
        </div>

        <!-- Right: Payment (35%) -->
        <div class="[flex-basis:35%] pl-4">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Payment</h2>
            <form @submit.prevent="submitOrder()">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Payment Date</label>
                    <input type="date" x-model="paymentData.payment_date"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                </div>

                 <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Payment Method</label>
                    <select x-model="paymentMethod"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                    </select>
                </div>

                <!-- Reference Number only visible if GCash -->
                <div class="mb-4" x-show="paymentMethod === 'GCash'">
                    <label class="block text-gray-700 font-medium mb-1">Reference Number</label>
                    <input type="text" x-model="paymentReference"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Amount</label>
                        <input type="text"
                            :value="formatMoney(paymentAmount)"
                            readonly
                            class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                    </div>

                    <!-- Cash -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Cash</label>
                        <input type="number"
                            x-model.number="paymentCash"
                            step="0.01"
                            :min="product_type === 'stockin_id' ? paymentAmount : 0"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400"
                            required>
                    </div>

                <!-- Dynamic Warning / Change Message -->
                <div x-show="paymentCash > 0" class="rounded-lg p-3 mb-4">
                   <!-- READY MADE -->
                    <template x-if="product_type === 'stockin_id'">
                        <div>
                            <template x-if="paymentCash < paymentAmount">
                                <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg p-3">
                                    <p class="font-semibold">❌ Full Payment Required</p>
                                    <p class="text-sm">
                                        Ready Made products require full payment. Amount needed: ₱
                                        <span x-text="formatMoney(paymentAmount)"></span>
                                    </p>
                                </div>
                            </template>
                            <template x-if="paymentCash >= paymentAmount">
                                <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-3">
                                    <p class="font-semibold">✅ Full Payment Received</p>
                                    <p class="text-sm">
                                        Change: ₱
                                        <span x-text="formatMoney(paymentCash - paymentAmount)"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- CUSTOMIZED ITEM -->
                    <template x-if="product_type === 'deliverydetails_id'">
                        <div>
                            <template x-if="paymentCash < paymentAmount">
                                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-3">
                                    <p class="font-semibold">⚠️ Partial Payment</p>
                                    <p class="text-sm">
                                        Remaining Balance: ₱
                                        <span x-text="formatMoney(paymentAmount - paymentCash)"></span>
                                    </p>
                                </div>
                            </template>
                            <template x-if="paymentCash >= paymentAmount">
                                <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-3">
                                    <p class="font-semibold">✅ Fully Paid</p>
                                    <p class="text-sm">
                                        Change: ₱
                                        <span x-text="formatMoney(paymentCash - paymentAmount)"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Payment Type Notice -->
                <div x-show="product_type === 'stockin_id'" class="bg-blue-50 border border-blue-300 text-blue-800 rounded-lg p-3 mb-4">
                    <p class="font-semibold">ℹ️ Ready Made Product</p>
                    <p class="text-sm">Full payment is required for ready-made items.</p>
                </div>

                <div x-show="product_type === 'deliverydetails_id'" class="bg-purple-50 border border-purple-300 text-purple-800 rounded-lg p-3 mb-4">
                    <p class="font-semibold">ℹ️ Customized Item</p>
                    <p class="text-sm">Partial payment is allowed for customized orders.</p>
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button" @click="showAddOrder=false"
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

<!-- Success toast (cloaked so it won't flash on reload) -->
<div
    x-show="showSuccess"
    x-cloak
    x-transition
    class="no-print fixed top-4 left-1/2 -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm z-[9999]"
>
    Your order was processed successfully.
</div>

<div
    x-show="showReceipt"
    x-transition
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
>
    <div id="printableReceipt"
         @click.away="showReceipt = false"
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

        <!-- Header: store + logo -->
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
                        class="object-contain max-w-full max-h-full"
                    >
                </div>
            </div>
        </div>

        <!-- Receipt # + date (uses payment id) -->
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
                        <span x-text="receipt.payment_date || paymentData.payment_date || 'N/A'"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Billed to + status -->
        <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
            <div>
                <p class="uppercase text-[10px] font-semibold text-gray-500">Billed To</p>
                <p class="font-semibold" x-text="receipt.customer_name"></p>
                <p x-text="receipt.customer_address ?? ''"></p>
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

        <!-- Totals (no VAT) -->
        <div class="mt-3 flex justify-end">
            <div class="w-56 text-[11px] space-y-0.5">
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

                <div class="flex justify-between pt-1">
                    <span>Cash</span>
                    <span>
                        ₱<span x-text="Number(receipt.cash || paymentCash || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Change</span>
                    <span>
                        ₱<span x-text="Number(receipt.change_amount || paymentChange || 0).toFixed(2)"></span>
                    </span>
                </div>

                <div class="flex justify-between" x-show="Number(receipt.balance || paymentBalance || 0) > 0">
                    <span>Balance</span>
                    <span class="text-red-600 font-semibold">
                        ₱<span x-text="Number(receipt.balance || paymentBalance || 0).toFixed(2)"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-4 text-[11px]">
            <p class="font-semibold mb-1">Notes</p>
            <p>
                Thank you for choosing Mariviles Graphic Studio.
                Your positive feedback helps us continue providing quality service.
            </p>
        </div>

        <!-- Authorized by -->
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
                    @click="showReceipt = false; showSuccess = true; window.location.reload()"
                    class="px-8 py-2 rounded-lg bg-yellow-400 text-black text-sm font-semibold hover:bg-yellow-500 transition">
                Close
            </button>
        </div>
    </div>
</div>

    </div>
</div>

<script>
function orderData() {
    return {
        selectedCustomer: '',
        product_type: '',
        selectedCategory: '',   // not used in payload

        // base value kept but not used directly now
        customAddOn: 50,

        items: [{
            selected_product_name: '',
            stock_id: '',
            color: '#000000',
            size: '',
            quantity: 1,
            price: 0,           // unit selling price with markup
            custom_amount: 0,   // customization fee per line
            total: 0,           // line total
            profit: 0,          // per‑unit profit (markup only)
            category_name: '',
            unavailable: false
        }],

        showReceipt: false,
        receipt: {
            status: '',
            customer_name: '',
            customer_address: '',
            receipt_number: null,   // will hold payment_id from backend
            payment_method: '',
            reference_number: null,
            payment_date: '',
            amount: 0,
            cash: 0,
            change_amount: 0,
            balance: 0,
            items: []
        },

        showSuccess: false,

        printReceipt() {
            window.print();
            this.showSuccess = true;
        },

        // inventory with unit_cost, markup_rule, category_name
        stockList: {
            @foreach($inventories as $stock)
            '{{ $stock->stock_id }}': {
                stock_id: '{{ $stock->stock_id }}',
                product_id: '{{ $stock->product_id }}',
                name: '{{ $stock->product->product_name ?? "No Product" }}',
                unit: '{{ $stock->product->unit ?? "" }}',
                unit_cost: {{ $stock->unit_cost ?? 0 }},
                markup_rule: {{ $stock->product->markup_rule ?? 0 }},
                current_stock: {{ $stock->current_stock ?? 0 }},
                stockin_id: '{{ $stock->stockin_id ?? "" }}',
                deliverydetails_id: '{{ $stock->deliverydetails_id ?? "" }}',
                category_id: '{{ $stock->product->category_id ?? "" }}',
                category_name: '{{ $stock->product->category->category_name ?? "" }}',
                size: '{{ $stock->size ?? "" }}',
                product_type: '{{ $stock->product_type ?? ($stock->type ?? ($stock->deliveryDetail ? $stock->deliveryDetail->type : "")) }}'
            },
            @endforeach
        },

        paymentData: { payment_date: '{{ date("Y-m-d") }}' },
        paymentCash: 0,
        paymentMethod: '',
        paymentReference: '',

        showAddCustomerModal: false,
        fname:'', mname:'', lname:'', contact_no:'', address:'',

        // helpers -------------------------------------------------

        formatMoney(value) {
            const n = Number(value || 0);
            return n.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        colorNameFromHex(hex) {
            if (!hex) return '';
            const value = String(hex).toLowerCase();

            const map = {
                '#000000': 'Black',
                '#ffffff': 'White',
                '#ff0000': 'Red',
                '#00ff00': 'Lime',
                '#0000ff': 'Blue',
                '#00ffff': 'Cyan',
                '#ffff00': 'Yellow',
                '#ffa500': 'Orange',
                '#800080': 'Purple',
                '#808080': 'Gray'
            };

            if (/^#([0-9a-f]{3})$/i.test(value)) {
                const r = value[1], g = value[2], b = value[3];
                const full = `#${r}${r}${g}{g}${b}${b}`;
                return map[full] || 'Custom Color';
            }

            return map[value] || 'Custom Color';
        },

        // product‑type helpers -------------------------------------

        selectedTypeKey() {
            if (this.product_type === 'stockin_id') return 'ready';
            if (this.product_type === 'deliverydetails_id') return 'custom';
            return '';
        },

        normalizeStockType(raw) {
            if (raw === null || raw === undefined) return '';
            const s = String(raw).toLowerCase().trim();
            if (s === 'ready made') return 'ready';
            if (s === 'customize item') return 'custom';
            if (s.includes('ready')) return 'ready';
            if (s.includes('custom')) return 'custom';
            return '';
        },

        matchesSelectedType(stock) {
            const sel = this.selectedTypeKey();
            if (!sel) return false;
            const stockKey = this.normalizeStockType(stock.product_type);
            return stockKey === sel;
        },

        // product & size options -----------------------------------

        getUniqueProducts() {
            if (!this.product_type) return [];
            const typeSuffix = this.selectedTypeKey() === 'ready'
                ? ' - Ready Made'
                : ' - Customized Item';

            const entries = Object.entries(this.stockList).map(([k,v]) => ({ ...v }));
            const filtered = entries.filter(s => this.matchesSelectedType(s));

            const unique = [];
            const seen = new Set();
            for (const s of filtered) {
                const name = s.name || '';
                if (!seen.has(name)) {
                    seen.add(name);
                    unique.push({
                        name,
                        label: name + typeSuffix,
                        product_id: s.product_id,
                        available: true
                    });
                }
            }

            this.items.forEach(it => {
                if (it.selected_product_name && !seen.has(it.selected_product_name)) {
                    seen.add(it.selected_product_name);
                    unique.push({
                        name: it.selected_product_name,
                        label: it.selected_product_name + typeSuffix,
                        product_id: null,
                        available: false
                    });
                }
            });

            return unique;
        },

        getAvailableSizesForProduct(productName) {
            if (!productName) return [];
            let stocks = Object.entries(this.stockList)
                .map(([k,v]) => ({ ...v }))
                .filter(s => s.name === productName);
            stocks = stocks.filter(s => this.matchesSelectedType(s));

            const sizeMap = new Map();
            stocks.forEach(s => {
                const size = s.size || 'One Size';
                const avail = Number(s.current_stock || 0);
                const existing = sizeMap.get(size);
                if (!existing || avail > Number(existing.stock || 0)) {
                    sizeMap.set(size, {
                        size,
                        stock: avail,
                        stock_id: s.stock_id,
                        disabled: avail <= 0
                    });
                }
            });

            const sizes = Array.from(sizeMap.values());
            const sizeOrder = ['Extra Small','Small','Medium','Large','Extra Large','Double XL','Triple XL',
                               '28','30','32','34','36','38','40','42','One Size'];
            sizes.sort((a,b) => {
                const ia = sizeOrder.indexOf(a.size);
                const ib = sizeOrder.indexOf(b.size);
                if (ia === -1 && ib === -1) return a.size.localeCompare(b.size);
                if (ia === -1) return 1;
                if (ib === -1) return -1;
                return ia - ib;
            });

            return sizes;
        },

        // selection handlers ---------------------------------------

        onProductSelect(item, index) {
            item.stock_id = '';
            item.size = '';
            item.price = 0;
            item.custom_amount = 0;
            item.total = 0;
            item.profit = 0;
            item.category_name = '';
            item.unavailable = false;

            const sizes = this.getAvailableSizesForProduct(item.selected_product_name, index);
            const firstAvailable = sizes.find(s => !s.disabled) || sizes[0];
            if (firstAvailable) {
                item.size = firstAvailable.size;
                item.stock_id = firstAvailable.stock_id;
                const stock = this.stockList[item.stock_id];
                item.category_name = stock ? (stock.category_name || '') : '';
                this.updateTotal(item);
            } else {
                item.unavailable = true;
            }
        },

        onSizeSelect(item) {
            if (!item.selected_product_name || !item.size) return;

            const matching = Object.entries(this.stockList).map(([k,v]) => ({ ...v }))
                .find(s =>
                    s.name === item.selected_product_name &&
                    (s.size || 'One Size') === item.size &&
                    this.matchesSelectedType(s)
                );

            if (matching) {
                item.stock_id = matching.stock_id;
                item.category_name = matching.category_name || '';
                this.updateTotal(item);
                item.unavailable = false;
            } else {
                item.stock_id = '';
                item.price = 0;
                item.custom_amount = 0;
                item.total = 0;
                item.profit = 0;
                item.category_name = '';
                item.unavailable = true;
            }
        },

        onProductTypeChange() {
            this.items.forEach(item => {
                item.selected_product_name = '';
                item.stock_id = '';
                item.color = '#000000';
                item.size = '';
                item.price = 0;
                item.custom_amount = 0;
                item.total = 0;
                item.profit = 0;
                item.category_name = '';
                item.unavailable = false;
            });
        },

        onFilterChange() {
            this.onProductTypeChange();
        },

        // item add/duplicate ---------------------------------------

        addNewItem() {
            this.items.push({
                selected_product_name: '',
                stock_id: '',
                color: '#000000',
                size: '',
                quantity: 1,
                price: 0,
                custom_amount: 0,
                total: 0,
                profit: 0,
                category_name: '',
                unavailable: false
            });
        },

        duplicateItem(index) {
            const itemToDuplicate = this.items[index];
            const newItem = JSON.parse(JSON.stringify(itemToDuplicate));
            newItem.unavailable = false;
            if (newItem.selected_product_name) {
                const sizes = this.getAvailableSizesForProduct(newItem.selected_product_name);
                const currentSizeIndex = sizes.findIndex(s => s.size === newItem.size);
                let nextSize = null;
                if (currentSizeIndex !== -1) {
                    for (let i = currentSizeIndex + 1; i < sizes.length; i++) {
                        if (!sizes[i].disabled) { nextSize = sizes[i]; break; }
                    }
                    if (!nextSize) {
                        for (let i = 0; i < currentSizeIndex; i++) {
                            if (!sizes[i].disabled) { nextSize = sizes[i]; break; }
                        }
                    }
                } else {
                    nextSize = sizes.find(s => !s.disabled) || sizes[0];
                }
                if (nextSize) {
                    newItem.size = nextSize.size;
                    newItem.stock_id = nextSize.stock_id;
                    const stock = this.stockList[newItem.stock_id];
                    newItem.category_name = stock ? (stock.category_name || '') : '';
                    this.updateTotal(newItem);
                }
            }
            this.items.push(newItem);
        },

        // pricing logic --------------------------------------------

        computeSellingPrice(stock) {
            const cost          = Number(stock.unit_cost || 0);
            const markupPercent = Number(stock.markup_rule || 0);
            const markup        = markupPercent / 100;
            return cost * (1 + markup);
        },

        // quantity-based customization fee per unit
        computeCustomFeePerUnit(qty) {
            const q = Number(qty || 0);
            if (q >= 1 && q <= 3) return 100;
            if (q >= 4 && q <= 6) return 80;
            if (q >= 7 && q <= 9) return 60;
            if (q >= 10)         return 50;
            return 0;
        },

        updateTotal(item) {
            if (item.stock_id && this.stockList[item.stock_id]) {
                const stock = this.stockList[item.stock_id];

                const cost         = Number(stock.unit_cost || 0);
                const pricePerUnit = this.computeSellingPrice(stock);
                const qty          = Number(item.quantity || 0);

                let customPerUnit = 0;
                if (this.selectedTypeKey() === 'custom') {
                    // tiered fee per piece
                    customPerUnit = this.computeCustomFeePerUnit(qty);
                }
                const customLine = customPerUnit * qty;

                const baseProfitPerUnit = pricePerUnit - cost; // markup profit (you can also add customPerUnit if you want)
                const lineTotal = (pricePerUnit * qty) + customLine;

                item.price         = Number(pricePerUnit.toFixed(2));
                item.custom_amount = Number(customLine.toFixed(2));  // total custom fee for this line (e.g. 50 * 10 = 500)
                item.total         = Number(lineTotal.toFixed(2));
                item.profit        = Number(baseProfitPerUnit.toFixed(2));
            } else {
                item.price = 0;
                item.custom_amount = 0;
                item.total = 0;
                item.profit = 0;
            }
        },

        // Grand Total (sum of item.total)
        grandTotal() {
            return Number(
                this.items.reduce((sum,i) => sum + Number(i.total || 0), 0).toFixed(2)
            );
        },

        // payment helpers ------------------------------------------

        get paymentAmount() { return this.grandTotal(); },
        get paymentChange() { return Math.max(Number(this.paymentCash || 0) - this.paymentAmount, 0).toFixed(2); },
        get paymentBalance() { return Math.max(this.paymentAmount - Number(this.paymentCash || 0), 0).toFixed(2); },
        get paymentStatus() { return Number(this.paymentCash || 0) >= this.paymentAmount ? 'Fully Paid' : 'Partial'; },

        // customer modal -------------------------------------------

        openCustomerModal() { this.showAddCustomerModal = true; },
        closeCustomerModal() {
            this.showAddCustomerModal = false;
            this.fname=''; this.mname=''; this.lname='';
            this.contact_no=''; this.address='';
        },

        addCustomer() {
            if (!this.fname.trim() || !this.lname.trim() || !this.contact_no.trim()) {
                alert('First Name, Last Name, and Contact No are required!');
                return;
            }
            fetch('{{ route('orders.customer.store') }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    fname:this.fname,
                    mname:this.mname,
                    lname:this.lname,
                    contact_no:this.contact_no,
                    address:this.address
                })
            })
            .then(res=>res.json())
            .then(data=>{
                if(!data.success){
                    alert('Failed to add customer!');
                    return;
                }
                const c = data.customer;
                let select = document.querySelector('select[name=customer_id]');
                if (select) {
                    let option = document.createElement('option');
                    option.value = c.customer_id;
                    option.textContent = `${c.fname} ${c.lname}`;
                    select.appendChild(option);
                    select.value = c.customer_id;
                }
                this.selectedCustomer = c.customer_id;
                this.closeCustomerModal();
                alert('Customer added successfully!');
            }).catch(err=>{
                console.error('Customer add error:',err);
                alert('Failed to add customer. Please try again.');
            });
        },

        // submit ---------------------------------------------------

        submitOrder() {
            if (!this.selectedCustomer) { alert('Please select a customer.'); return; }
            if (!this.product_type)    { alert('Please select a product type (Ready Made or Customized Item).'); return; }
            if (this.items.length === 0 || !this.items.some(i => i.stock_id)) { alert('Please add at least one item.'); return; }
            if (!this.paymentMethod)   { alert('Please select a payment method.'); return; }

            // Ready made must be fully paid
            if (this.selectedTypeKey() === 'ready' &&
                Number(this.paymentCash || 0) < this.paymentAmount) {
                alert('Full payment is required for Ready Made products!');
                return;
            }

            const payload = {
                customer_id: this.selectedCustomer,
                order_date: this.paymentData.payment_date,
                product_type: this.product_type,
                items: this.items.map(i => ({
                    stock_id: i.stock_id,
                    color:   i.color || null,
                    size:    i.size  || null,
                    quantity: i.quantity,
                    price:    i.price,
                    custom_amount: i.custom_amount,
                    total:    i.total,
                    profit:   i.profit
                })),
                total_amount: this.paymentAmount,
                cash: this.paymentCash,
                payment_method: this.paymentMethod,
                reference_number: this.paymentReference || null
            };

            fetch('{{ route("orders.store") }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res=>res.json())
            .then(data=>{
                if(!data.success){
                    console.log(data.errors || data.message);
                    alert(data.message || 'Failed to create order and payment');
                    return;
                }

                const order   = data.order   || {};
                const payment = data.payment || {};

                // Receipt number from saved payment
                this.receipt.receipt_number = payment.payment_id || payment.id || null;

                // Items for receipt
                this.receipt.items = this.items
                    .filter(i => i.stock_id)
                    .map(i => {
                        const stock = this.stockList[i.stock_id] || {};
                        const qty   = Number(i.quantity || 1);
                        const unitPrice = Number(i.price || 0);
                        const amount    = Number(i.total || 0);
                        return {
                            quantity: qty,
                            product_name: stock.name || '',
                            product_id: stock.product_id || null,
                            stock_id: stock.stock_id || null,
                            size: i.size,
                            color: i.color,
                            unit_price: unitPrice,
                            custom_amount: i.custom_amount,
                            amount: amount
                        };
                    });

                this.receipt.status           = this.paymentStatus;
                const select = document.querySelector('select[name=customer_id]');
                const opt    = select ? select.options[select.selectedIndex] : null;
                this.receipt.customer_name    = opt ? opt.textContent : '';
                this.receipt.payment_method   = this.paymentMethod;
                this.receipt.reference_number = this.paymentReference || null;
                this.receipt.payment_date     = this.paymentData.payment_date;
                this.receipt.amount           = this.paymentAmount;
                this.receipt.cash             = this.paymentCash;
                this.receipt.change_amount    = this.paymentChange;
                this.receipt.balance          = this.paymentBalance;

                this.showReceipt = true;
            })
            .catch(err=>{
                console.error('Submit error:', err);
                alert('An error occurred. Please try again.');
            });
        }
    }
}
</script>
