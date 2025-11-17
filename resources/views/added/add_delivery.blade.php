<div x-show="showAddDelivery" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">

    <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl p-8 relative" 
         x-data="{
            selectedSupplier: '',
            products: [{ product_id:'', quantity_product:1, unit_cost:0, total:0 }],

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
                this.products = [{ product_id:'', quantity_product:1, unit_cost:0, total:0, unit:'' }];
            }
         }"
    >

    <h2 class="text-2xl font-bold mb-4 text-gray-800 flex justify-between items-center">

        <!-- Left: Title -->
        <span>Add New Delivery</span>

        <!-- Right: Requested by (thin text) -->
        <span class="text-sm font-light text-gray-600">
            Requested by: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
        </span>

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

             <!-- Products Table -->
            <div class="mb-4">
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

                                <!-- Quantity -->
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

                                <!-- Remove Row -->
                                <td class="px-2 py-2 border text-center">
                                    <button type="button"
                                            @click="products.splice(index, 1)"
                                            class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="25" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M3 4h18v4H3z" />
                                            <path d="M4 8v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                            <path d="M10 12h4" />
                                        </svg>
                                    </button>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Add New Product -->
                <button type="button"
                        @click="products.push({product_id:'', quantity_product:1, unit:'', unit_cost:0, total:0})"
                        class="mt-2 bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                    Add Product
                </button>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">
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
