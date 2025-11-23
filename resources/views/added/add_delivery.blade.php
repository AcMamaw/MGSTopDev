<div x-show="showAddDelivery" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl relative
                overflow-y-auto max-h-[calc(100vh-2rem)] p-8"
         x-data="{
            selectedSupplier: '',
            products: [{ product_id:'', quantity_product:1, unit:'', unit_cost:0, total:0 }],

            supplierProducts: {
                @foreach($suppliers as $supplier)
                    '{{ $supplier->supplier_id }}': [
                        @foreach($products->where('supplier_id', $supplier->supplier_id) as $product)
                            { id: '{{ $product->product_id }}', name: '{{ $product->product_name }}' },
                        @endforeach
                    ],
                @endforeach
            },

            productUnits: {
                @foreach($products as $product)
                    '{{ $product->product_id }}': '{{ $product->unit }}',
                @endforeach
            },

            productPrices: {
                @foreach($products as $product)
                    '{{ $product->product_id }}': {{ $product->price }},
                @endforeach
            },

            updateTotal(item) {
                let price = this.productPrices[item.product_id] || 0;
                item.unit_cost = price;
                item.total = (price * item.quantity_product).toFixed(2);
                item.unit = this.productUnits[item.product_id] || '';
            },

            resetProducts() {
                this.products = [{ product_id:'', quantity_product:1, unit:'', unit_cost:0, total:0 }];
            },

            grandTotal() {
                return this.products.reduce((sum, item) => {
                    return sum + Number(item.total || 0);
                }, 0).toFixed(2);
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
                            <th class="px-4 py-2 border w-20">Qty</th>
                            <th class="px-4 py-2 border w-24">Unit</th>
                            <th class="px-4 py-2 border w-40">Cost</th>
                            <th class="px-4 py-2 border w-40">Total</th>
                            <th class="px-4 py-2 border">Action</th>
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
                                        @change="updateTotal(item)"
                                        required
                                        class="w-full px-2 py-1 border rounded">
                                        <option value="">Select</option>
                                        <template x-for="p in (supplierProducts[selectedSupplier] || [])" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- Qty -->
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'products['+index+'][quantity_product]'"
                                           x-model="item.quantity_product" min="1"
                                           @input="updateTotal(item)"
                                           class="w-full px-2 py-1 border rounded text-right">
                                </td>

                                <!-- Unit -->
                                <td class="px-2 py-2 border">
                                    <input type="text"
                                        :name="'products['+index+'][unit]'"
                                        x-model="item.unit"
                                        readonly
                                        class="w-full px-2 py-1 border rounded bg-gray-100 text-center">
                                </td>

                                <!-- Unit Cost -->
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'products['+index+'][unit_cost]'"
                                           x-model="item.unit_cost" readonly
                                           class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>

                                <!-- Total -->
                                <td class="px-2 py-2 border">
                                    <input type="number"
                                           :name="'products['+index+'][total]'"
                                           x-model="item.total" readonly
                                           class="w-full px-2 py-1 border rounded bg-gray-100 text-right">
                                </td>

                                <!-- Action -->
                                <td class="px-2 py-2 border text-center">
                                    <button type="button"
                                            @click="products.splice(index, 1)"
                                            class="w-10 h-10 flex items-center justify-center ml-3 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="3" y1="6" x2="27" y2="6" />
                                            <rect x="6" y="6" width="18" height="18" rx="2" ry="2" />
                                            <line x1="10" y1="10" x2="10" y2="22" />
                                            <line x1="15" y1="10" x2="15" y2="22" />
                                            <line x1="20" y1="10" x2="20" y2="22" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <button type="button"
                        @click="products.push({product_id:'', quantity_product:1, unit:'', unit_cost:0, total:0})"
                        class="mt-2 bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Add Product
                </button>
            </div>

           <!-- Footer: Requested By + Grand Total -->
            <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">

                <!-- Requested By on the left -->
                <div class="text-lg font-semibold">
                    Requested by: {{ auth()->user()->employee->fname ?? '-' }} {{ auth()->user()->employee->lname ?? '-' }}
                </div>

                <!-- Grand Total on the right -->
                <div class="text-xl font-bold text-right">
                    Grand Total: ₱ <span x-text="grandTotal()"></span>
                    <input type="hidden" name="total_amount" :value="grandTotal()">
                </div>

            </div>

              <br>
            <!-- Buttons -->
            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" @click="showAddDelivery = false"
                        class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
