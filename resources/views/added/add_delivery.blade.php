<div x-show="showAddDelivery" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl relative
                overflow-y-auto max-h-[calc(100vh-3rem)] p-6"
         x-data="{
            selectedSupplier: '',
            deliveryType: '',
            products: [{ product_id:'', quantity_product:1, unit:'pcs', unit_cost:0, total:0, size:'', product_type:'Ready Made' }],

            supplierProducts: {
                @foreach($suppliers as $supplier)
                    '{{ $supplier->supplier_id }}': [
                        @foreach($products->where('supplier_id', $supplier->supplier_id) as $product)
                            { id: '{{ $product->product_id }}', name: '{{ $product->product_name }}', unit: '{{ $product->unit }}' },
                        @endforeach
                    ],
                @endforeach
            },

            getAvailableProducts() {
                // Return empty array if no supplier selected
                if (!this.selectedSupplier) {
                    return [];
                }
                // Return products for selected supplier
                return this.supplierProducts[this.selectedSupplier] || [];
            },

            updateUnit(item) {
                if (item.product_id) {
                    const product = (this.supplierProducts[this.selectedSupplier] || []).find(p => p.id == item.product_id);
                    item.unit = product?.unit || 'pcs';
                    item.unit_cost = 0;
                    item.total = (item.quantity_product * item.unit_cost).toFixed(2);
                } else {
                    item.unit = '';
                    item.unit_cost = 0;
                    item.total = 0;
                }
            },

            updateTotal(item) {
                item.total = (item.quantity_product * item.unit_cost).toFixed(2);
            },

            resetProducts() {
                this.products = [{ product_id:'', quantity_product:1, unit:'pcs', unit_cost:0, total:0, size:'', product_type:'Ready Made' }];
            },

            grandTotal() {
                return this.products.reduce((sum, item) => sum + Number(item.total || 0), 0).toFixed(2);
            }
         }"
    >

        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex justify-between items-center">
            <span>Add New Delivery</span>
        </h2>

        <form id="add-delivery-form" method="POST" action="{{ route('deliveries.store') }}">
            @csrf

            <!-- Supplier -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Supplier</label>
                <select name="supplier_id"
                        x-model="selectedSupplier"
                        @change="resetProducts()"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Type (Global - applies to all products) -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Product Type</label>
                <select name="product_type" 
                        x-model="deliveryType"
                        @change="products.forEach(p => p.product_type = deliveryType)"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Product Type</option>
                    <option value="Ready Made">Ready Made</option>
                    <option value="Customize Item">Customize Item</option>
                </select>
            </div>

            <!-- Date Requested -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Date Requested</label>
                <input type="date" name="delivery_date_request" required
                    value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
            </div>

            <!-- Product Table -->
            <div class="mb-4 overflow-x-auto">
                <label class="block text-gray-700 font-medium mb-2">Products</label>
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border w-48">Product</th>
                            <th class="px-4 py-2 border w-32">Size</th>
                            <th class="px-4 py-2 border w-20">Qty</th>
                            <th class="px-4 py-2 border w-32">Cost</th>
                            <th class="px-4 py-2 border w-24">Unit</th>
                            <th class="px-4 py-2 border w-32">Total</th>
                            <th class="px-4 py-2 border w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in products" :key="index">
                            <tr>
                                <!-- Product -->
                                <td class="px-2 py-2 border">
                                    <select 
                                        :name="'products['+index+'][product_id]'"
                                        x-model="item.product_id"
                                        @change="updateUnit(item)"
                                        required
                                        :disabled="!selectedSupplier"
                                        class="w-full px-2 py-1 border rounded"
                                        :class="!selectedSupplier ? 'bg-gray-100 cursor-not-allowed' : ''">
                                        <option value="">Select Product</option>
                                        <template x-for="p in getAvailableProducts()" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                    <!-- Hidden input to store product_type for each item -->
                                    <input type="hidden" :name="'products['+index+'][product_type]'" x-model="item.product_type">
                                </td>

                                <!-- Size -->
                                <td class="px-2 py-2 border">
                                    <select :name="'products['+index+'][size]'" 
                                            x-model="item.size"
                                            class="w-full px-2 py-1 border rounded text-sm bg-white">
                                        <option value="">Select Size</option>
                                        <option value="Extra Small">Extra Small</option>
                                        <option value="Small">Small</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Large">Large</option>
                                        <option value="Extra Large">Extra Large</option>
                                        <option value="Double XL">Double XL</option>
                                        <option value="Triple XL">Triple XL</option>
                                        <option value="28">28</option>
                                        <option value="30">30</option>
                                        <option value="32">32</option>
                                        <option value="34">34</option>
                                        <option value="36">36</option>
                                        <option value="38">38</option>
                                        <option value="40">40</option>
                                        <option value="42">42</option>
                                        <option value="One Size">One Size</option>
                                    </select>
                                </td>

                                <!-- Quantity -->
                                <td class="px-2 py-2 border">
                                    <input type="number" min="1"
                                        :name="'products['+index+'][quantity_product]'"
                                        x-model="item.quantity_product"
                                        @input="updateTotal(item)"
                                        required
                                        class="w-full px-2 py-1 border rounded text-right">
                                </td>

                                <!-- Unit Cost -->
                                <td class="px-2 py-2 border">
                                    <input type="number" min="0" step="0.01"
                                        :name="'products['+index+'][unit_cost]'"
                                        x-model="item.unit_cost"
                                        @input="updateTotal(item)"
                                        required
                                        class="w-full px-2 py-1 border rounded text-right">
                                </td>

                                <!-- Unit (readonly) -->
                                <td class="px-2 py-2 border">
                                    <input type="text" readonly
                                        :name="'products['+index+'][unit]'"
                                        x-model="item.unit"
                                        class="w-full px-2 py-1 border rounded bg-gray-100 text-center text-sm">
                                </td>

                                <!-- Total (readonly) -->
                                <td class="px-2 py-2 border">
                                    <input type="number" readonly
                                        :name="'products['+index+'][total]'"
                                        x-model="item.total"
                                        class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>

                                <!-- Action (Delete) -->
                                <td class="px-2 py-2 border text-center">
                                    <button type="button"
                                            @click="products.splice(index, 1)"
                                            :disabled="products.length === 1"
                                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200"
                                            :class="products.length === 1 ? 'opacity-50 cursor-not-allowed' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="3" y1="6" x2="21" y2="6" />
                                            <rect x="6" y="6" width="12" height="14" rx="2" />
                                            <line x1="10" y1="10" x2="10" y2="18" />
                                            <line x1="14" y1="10" x2="14" y2="18" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Add Product Button -->
                <button type="button"
                        @click="products.push({product_id:'', quantity_product:1, unit:'pcs', unit_cost:0, total:0, size:'', product_type:'Ready Made'})"
                        :disabled="!selectedSupplier"
                        class="mt-2 px-6 py-2 rounded transition-colors"
                        :class="selectedSupplier ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                    + Add Product
                </button>
            </div>

            <!-- Footer -->
            <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">
                <div class="text-lg font-semibold">
                    Requested by: {{ auth()->user()->employee->fname ?? '-' }} {{ auth()->user()->employee->lname ?? '-' }}
                </div>
                <div class="text-xl font-bold text-right">
                    Grand Total: ₱ <span x-text="grandTotal()"></span>
                    <input type="hidden" name="total_amount" :value="grandTotal()">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-wrap justify-end gap-2 mt-4">
                <button type="button" @click="showAddDelivery = false"
                        class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600 transition">
                    Save Delivery
                </button>
            </div>
        </form>

    </div>
</div>