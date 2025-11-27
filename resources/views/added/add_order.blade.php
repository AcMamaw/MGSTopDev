<!-- Combined Add Order + Payment Modal -->
<div x-show="showAddOrder" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl relative overflow-y-auto max-h-[calc(100vh-2rem)] p-6 flex gap-6"
         x-data="orderData()">

        <!-- Left: Add Order (65%) -->
        <div class="[flex-basis:65%] border-r pr-4 flex flex-col">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Add New Order</h2>

            <!-- Customer Selection -->
            <div class="flex flex-col md:flex-row items-center gap-2 mb-4">
                <select name="customer_id" x-model="selectedCustomer" required
                        class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">
                            {{ $customer->fname }} {{ $customer->lname }}
                        </option>
                    @endforeach
                </select>
                <button type="button" @click.stop="openCustomerModal()"
                        class="px-4 h-10 text-sm rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                    Add Customer
                </button>
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
                                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
                        <button type="button" @click="addCustomer()"
                                class="px-6 py-2 rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Type & Category -->
            <div class="flex flex-col md:flex-row items-center gap-2 mb-4">

                <!-- Product Type -->
                <select name="product_type" x-model="product_type" 
                        @change="onFilterChange()"
                        class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Product Type</option>
                    <option value="stockin_id">Ready Made</option>
                    <option value="deliverydetails_id">Customized Item</option>
                </select>

                <!-- Category -->
                <select name="category_id" x-model="selectedCategory"
                        @change="onFilterChange()"
                        class="w-full md:flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_id }}">
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>

            </div>

          <!-- Order Items (Card Layout) -->
            <div class="space-y-4">

                <template x-for="(item, index) in items" :key="index">
                    <div class="bg-white border rounded-xl shadow p-4">

                        <!-- Card Header -->
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">Item <span x-text="index + 1"></span></h3>

                            <div class="flex space-x-2">
                                <!-- Duplicate Button -->
                                <button type="button"
                                        @click="duplicateItem(index)"
                                        class="text-gray-400 hover:text-blue-600 hover:bg-blue-100 p-2 rounded-full transition"
                                        title="Duplicate Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button type="button"
                                        @click="items.splice(index, 1)"
                                        class="text-gray-400 hover:text-red-600 hover:bg-red-100 p-2 rounded-full transition">
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

                     <!-- Inputs Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

                           <!-- Product (wider) -->
                            <div class="md:col-span-6">
                                <label class="text-sm font-medium text-gray-700">Product</label>
                                <select :name="'items['+index+'][stock_id]'"
                                        x-model="item.stock_id"
                                        @change="updateTotal(item); updateColorSizeOptions(item)"
                                        :disabled="!product_type"
                                        class="w-full mt-1 px-2 py-2 border rounded-lg bg-white disabled:bg-gray-100 text-sm">
                                    <option value="">Select Product</option>
                                    <template x-for="stock in getAvailableStocksForItem(index)" :key="stock.stock_id">
                                        <!-- Add :selected if editing existing item -->
                                        <option :value="stock.stock_id" 
                                                :selected="stock.stock_id === item.stock_id"
                                                x-text="stock.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Size (wider) -->
                            <div class="md:col-span-5">
                                <label class="text-sm font-medium text-gray-700">Size</label>
                                <select :name="'items['+index+'][size]'"
                                        x-model="item.size"
                                        :disabled="!item.stock_id"
                                        class="w-full mt-1 px-2 py-2 border rounded-lg bg-white disabled:bg-gray-100 text-sm">
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
                            </div>

                            <!-- Color (smaller) -->
                            <div class="md:col-span-1 flex flex-col">
                                <label class="text-sm font-medium text-gray-700">Color</label>
                                <input type="color"
                                    :name="'items['+index+'][color]'"
                                    x-model="item.color"
                                    :disabled="!item.stock_id"
                                    class="w-full h-9 mt-1 border rounded cursor-pointer disabled:bg-gray-100">
                            </div>

                        </div>

                        <!-- Second Row Inputs -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">

                            <!-- Available Stock -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Available</label>
                                <div class="mt-1 px-3 py-2 border bg-gray-50 rounded-lg text-gray-700">
                                    <span x-text="item.stock_id ? (stockList[item.stock_id]?.current_stock || '-') : '-'"></span>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" min="1"
                                    x-model.number="item.quantity"
                                    @input="updateTotal(item)"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg text-right">
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Unit Price</label>
                                <input type="number" readonly
                                    x-model="item.price"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-right">
                            </div>

                            <!-- Total -->
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total</label>
                                <input type="number" readonly
                                    x-model="item.total"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100 text-right">
                            </div>

                        </div>

                    </div>
                </template>

                <!-- Add Item Button -->
              <div class="w-full flex justify-center mt-3">
                    <button type="button"
                            @click="addNewItem()"
                            class="bg-yellow-500 text-white px-5 py-2 rounded-lg text-lg font-semibold hover:bg-yellow-600 transition">
                        + Add Item
                    </button>
                </div>
            <br>
            </div>

            <!-- Footer: Issued By + Grand Total -->
            <div class="mt-auto pt-4 border-t flex justify-between items-center text-gray-800">
                
                <!-- Issued By on the left -->
                <div class="text-lg font-semibold">
                   Issued by: {{ auth()->user()->employee->fname }} {{ auth()->user()->employee->lname }}
                </div>

                <!-- Grand Total on the right -->
                <div class="text-xl font-bold">
                    Grand Total: ₱ <span x-text="grandTotal()"></span>
                </div>
            </div>
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
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="text" :value="paymentAmount.toFixed(2)" readonly
                        class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                </div>


                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Cash</label>
                    <input type="number" x-model.number="paymentCash" step="0.01" 
                        :min="product_type === 'stockin_id' ? paymentAmount : 0"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-400" required>
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

                <!-- Dynamic Warning / Change Message -->
                <div x-show="paymentCash > 0" class="rounded-lg p-3 mb-4">
                    <!-- READY MADE: Must be fully paid -->
                    <template x-if="product_type === 'stockin_id'">
                        <div>
                            <template x-if="paymentCash < paymentAmount">
                                <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg p-3">
                                    <p class="font-semibold">❌ Full Payment Required</p>
                                    <p class="text-sm">Ready Made products require full payment. Amount needed: ₱<span x-text="paymentAmount.toFixed(2)"></span></p>
                                </div>
                            </template>
                            <template x-if="paymentCash >= paymentAmount">
                                <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-3">
                                    <p class="font-semibold">✅ Full Payment Received</p>
                                    <p class="text-sm">Change: ₱<span x-text="(paymentCash - paymentAmount).toFixed(2)"></span></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- CUSTOMIZED ITEM: Partial payment allowed -->
                    <template x-if="product_type === 'deliverydetails_id'">
                        <div>
                            <template x-if="paymentCash < paymentAmount">
                                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-3">
                                    <p class="font-semibold">⚠️ Partial Payment</p>
                                    <p class="text-sm">Remaining Balance: ₱<span x-text="(paymentAmount - paymentCash).toFixed(2)"></span></p>
                                </div>
                            </template>
                            <template x-if="paymentCash >= paymentAmount">
                                <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-3">
                                    <p class="font-semibold">✅ Fully Paid</p>
                                    <p class="text-sm">Change: ₱<span x-text="(paymentCash - paymentAmount).toFixed(2)"></span></p>
                                </div>
                            </template>
                        </div>
                    </template>
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

               <div class="flex justify-center gap-3">
                    <button type="button" @click="showAddOrder=false"
                            class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500 transition">
                        Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function orderData() {
    return {
        selectedCustomer: '',
        product_type: '',
        selectedCategory: '',
        items: [{ 
            stock_id: '', 
            color: '',   // match x-model in template
            size: '',    // match x-model in template
            quantity: 1, 
            price: 0, 
            total: 0,
            availableColors: [],
            availableSizes: []
        }],
        
        stockList: {
            @foreach($inventories as $stock)
            '{{ $stock->stock_id }}': {
                product_id: '{{ $stock->product_id }}',
                name: '{{ $stock->product->product_name ?? "No Product" }}',
                unit: '{{ $stock->product->unit ?? "" }}',
                price: {{ $stock->unit_cost ?? 0 }},
                current_stock: {{ $stock->current_stock ?? 0 }},
                stockin_id: '{{ $stock->stockin_id ?? "" }}',
                deliverydetails_id: '{{ $stock->deliverydetails_id ?? "" }}',
                category_id: '{{ $stock->product->category_id ?? "" }}'
            },
            @endforeach
        },

        paymentData: {
            payment_date: '{{ date("Y-m-d") }}',
        },
        paymentCash: 0,
        paymentMethod: '',
        paymentReference: '',

        // Customer modal
        showAddCustomerModal: false,
        fname:'', mname:'', lname:'', contact_no:'', address:'',

        // Get available stocks for dropdown
        getAvailableStocksForItem(currentIndex) {
            let filtered = Object.entries(this.stockList).map(([stock_id, stock]) => ({
                stock_id,
                ...stock
            }));

            if (this.product_type === 'stockin_id') {
                filtered = filtered.filter(s => s.stockin_id && s.stockin_id !== '');
            } else if (this.product_type === 'deliverydetails_id') {
                filtered = filtered.filter(s => s.deliverydetails_id && s.deliverydetails_id !== '');
            }

            // ✅ Allow duplicates by returning all filtered stocks
            return filtered;
        },

        onFilterChange() {
            this.items.forEach(item => {
                item.stock_id = '';
                item.color = '';
                item.size = '';
                item.price = 0;
                item.total = 0;
                item.availableColors = [];
                item.availableSizes = [];
            });
        },

        updateColorSizeOptions(item) {
            if (!item.stock_id) {
                item.availableColors = [];
                item.availableSizes = [];
                item.color = '';
                item.size = '';
                return;
            }
            item.availableColors = this.colorsList;
            item.availableSizes = this.sizesList;
        },

        addNewItem() {
            this.items.push({ 
                stock_id: '', 
                color: '',
                size: '',
                quantity: 1, 
                price: 0, 
                total: 0,
                availableColors: [],
                availableSizes: []
            });
        },

        duplicateItem(index) {
            const itemToDuplicate = this.items[index];
            const newItem = JSON.parse(JSON.stringify(itemToDuplicate));
            this.items.push(newItem);
            this.updateTotal(newItem);
        },

        openCustomerModal() { this.showAddCustomerModal = true; },
        closeCustomerModal() { 
            this.showAddCustomerModal = false; 
            this.fname=''; this.mname=''; this.lname=''; this.contact_no=''; this.address=''; 
        },

        addCustomer() {
            if(!this.fname.trim() || !this.lname.trim() || !this.contact_no.trim()) { 
                alert('First Name, Last Name, and Contact No are required!'); 
                return; 
            }
            fetch('{{ route('orders.customer.store') }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({ fname:this.fname, mname:this.mname, lname:this.lname, contact_no:this.contact_no, address:this.address })
            })
            .then(res=>res.json())
            .then(data=>{
                if(!data.success){ alert('Failed to add customer!'); return; }
                const c = data.customer;
                let select = document.querySelector('select[name=customer_id]');
                let option = document.createElement('option');
                option.value = c.customer_id;
                option.textContent = `${c.fname} ${c.lname}`;
                select.appendChild(option);
                select.value = c.customer_id;
                this.selectedCustomer = c.customer_id;
                this.closeCustomerModal();
            })
            .catch(err=>console.error(err));
        },

        updateTotal(item) {
            if(item.stock_id && this.stockList[item.stock_id]) {
                item.price = this.stockList[item.stock_id].price;
                item.total = (item.quantity * item.price).toFixed(2);
            } else {
                item.price = 0;
                item.total = 0;
            }
        },

        grandTotal() {
            return this.items.reduce((sum, i) => sum + Number(i.total || 0), 0).toFixed(2);
        },

        get paymentAmount() { return Number(this.grandTotal()); },
        get paymentChange() { return Math.max(this.paymentCash - this.paymentAmount, 0).toFixed(2); },
        get paymentBalance() { return Math.max(this.paymentAmount - this.paymentCash, 0).toFixed(2); },
        get paymentStatus() { return this.paymentCash >= this.paymentAmount ? 'Fully Paid' : 'Partial'; },

        submitOrder() {
            if(!this.selectedCustomer) { alert('Please select a customer.'); return; }
            if(!this.selectedCategory) { alert('Please select a category.'); return; }
            if(!this.product_type) { alert('Please select a product type (Ready Made or Customized Item).'); return; }
            if(this.items.length === 0 || !this.items[0].stock_id) { alert('Please add at least one item.'); return; }
            if(this.product_type === 'stockin_id' && this.paymentCash < this.grandTotal()) {
                alert('Full payment is required for Ready Made products!'); return;
            }

            const payload = {
                customer_id: this.selectedCustomer,
                category_id: this.selectedCategory,
                order_date: this.paymentData.payment_date,
                product_type: this.product_type,
                items: this.items.map(i => ({
                    stock_id: i.stock_id,
                    color: i.color || null,  
                    size: i.size || null,    
                    quantity: i.quantity,
                    price: i.price 
                })),
                total_amount: this.grandTotal(), 
                cash: this.paymentCash,
                payment_method: this.paymentMethod,
                reference_number: this.paymentReference || null
            };

            fetch('{{ route("orders.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    alert(data.message || 'Failed to create order and payment');
                    return;
                }
                alert('Order and Payment successfully created!');
                window.location.href = '{{ route("purchaseorder") }}';
            })
            .catch(err => console.error(err));
        }
    }
}
</script>
